<?php
chdir( __DIR__ );
require_once '../../../../../config/secrets.php';
require_once '../../../lp-config/variables/wikis.php';

$db = null;
try {
	$db = new PDO(
		'mysql:host=' . $dbCredentials['wiki']['host'] .
		';dbname=' . $dbCredentials['wiki']['database'] .
		';charset=utf8mb4',
		$dbCredentials['wiki']['user'],
		$dbCredentials['wiki']['pass']
	);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$db->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
	$db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
} catch ( PDOException $e ) {
	echo "Connection Error: " . $e->getMessage();
}
if ( $db === null ) {
	die( 'Could not connect to database' );
}

$wiki_hits = [];

$ch = curl_init();
curl_setopt( $ch, CURLOPT_HTTPHEADER, [
	'Host: liquipedia.net',
	'User-Agent: wiki-hotness/0.1',
	'Sec-Purpose: prefetch;anonymous-client-ip'
] );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_FAILONERROR, true );
curl_setopt( $ch, CURLOPT_ENCODING, "" );

foreach ( $liquipedia_wikis as $wiki => $info ) {
	$time = $info[ 'hot_threshold' ];
	$dbPrefix = $info[ 'db_prefix' ];

	print "Processing $wiki (threshold: $time, database: $dbPrefix)...\n";

	$db->prepare(
		'DELETE FROM `wiki_hits` ' .
		'WHERE `datum` < UNIX_TIMESTAMP(NOW() - INTERVAL ' . $time . ' ) ' .
		'AND `wiki` = :wiki' )->execute( [ 'wiki' => $wiki ] );

	$sql = 'SELECT url, COUNT(*) AS count FROM wiki_hits WHERE wiki = :wiki GROUP BY url ORDER BY count DESC LIMIT 15';
	$sqlStmt = $db->prepare( $sql );
	$sqlStmt->execute( [ 'wiki' => $wiki ] );
	$res = $sqlStmt->fetchAll();

	$num_found = 0;
	foreach ( $res as $row ) {
		$page = $row[ 'url' ];

		if ( $page == "Main_Page" ) {
			continue;
		}

		if ( !strncmp( $page, "Special:", 8 ) ) {
			continue;
		}

		$oldTextSql = 'SELECT t.old_text, pp.pp_value AS display_title '
			. 'FROM ' . $dbPrefix . 'page p '
			. 'JOIN ' . $dbPrefix . 'flaggedpages f ON f.fp_page_id = p.page_id '
			. 'JOIN ' . $dbPrefix . 'slots s ON s.slot_revision_id = f.fp_stable '
			. 'JOIN ' . $dbPrefix . 'content c ON c.content_id = s.slot_content_id '
			. 'JOIN ' . $dbPrefix . 'text t ON t.old_id = SUBSTR(c.content_address, 4) '
			. 'LEFT JOIN ' . $dbPrefix . 'page_props pp '
			. 'ON pp.pp_page = p.page_id AND pp.pp_propname = \'displaytitle\' '
			. 'WHERE p.page_namespace IN (0, 134) '
			. 'AND p.page_is_redirect = 0 '
			. 'AND p.page_title = :pageTitle';

		$oldTextStmt = $db->prepare( $oldTextSql );
		$oldTextStmt->execute( [ 'pageTitle' => $page ] );
		$oldText = $oldTextStmt->fetch();
		if ( !$oldText ) {
			continue;
		}

		// displaytitle is stored as safe HTML; convert it to the text used in <title>.
		if ( !empty( $oldText[ 'display_title' ] ) ) {
			$display_title = strip_tags( $oldText[ 'display_title' ] );
			$display_title = html_entity_decode(
				$display_title,
				ENT_QUOTES | ENT_HTML5,
				'UTF-8'
			);
			$display_title = preg_replace( '/[ \r\n\t]+/', ' ', $display_title ) ?? '';
			$display_title = trim( $display_title );
			if ( $display_title === '' ) {
				$display_title = str_replace( '_', ' ', $page );
			}
		} else {
			// MediaWiki uses the canonical page title when DISPLAYTITLE is not set.
			$display_title = str_replace( '_', ' ', $page );
		}

		if ( preg_match( '/\|tickername=(.+?)[\|}\r\n]/', $oldText[ 'old_text' ], $m ) ) {
			$ticker_title = trim( $m[ 1 ] );
		}

		// Try to use display title without ending disambiguation brackets if it's too long
		if ( mb_strlen( $display_title ) > 45 ) {
			$new_title = preg_replace( "/\s+\(.+\)$/u", "", $display_title );
			if ( !empty( $new_title ) ) {
				$display_title = $new_title;
			}
		}

		if ( !empty( $display_title ) && mb_strlen( $display_title ) <= 35 ) {
			$title = $display_title;
		} elseif ( !empty( $ticker_title ) ) {
			$title = $ticker_title;
		} elseif ( !empty( $display_title ) ) {
			$title = $display_title;
		} else {
			$title = str_replace( '_', ' ', $page );
		}

		$wiki_hits[] = [
			'title' => $title,
			'url' => $row[ 'url' ],
			'wiki' => $wiki,
			'hits' => (int)$row[ 'count' ],
		];

		print "Found {$row[ 'url' ]} ($title) - {$row[ 'count' ]}\n";
		if ( $num_found >= 10 ) {
			break;
		}

		unset( $display_title );
		unset( $ticker_title );
	}
}

for ( $attempts = 0; $attempts < 3; $attempts++ ) {
	$db->beginTransaction();

	$db->exec( 'DELETE FROM wiki_hot' );

	$insertSql = 'INSERT INTO wiki_hot (wiki, page, title, hits) VALUES ( :wiki, :url, :title, :count )';
	$sth = $db->prepare( $insertSql );

	foreach ( $wiki_hits as $row ) {

		$url = mb_substr( $row[ 'url' ], 0, 255 );
		$count = $row[ 'hits' ];
		$wiki = $row[ 'wiki' ];
		$title = mb_substr( $row[ 'title' ], 0, 255 );

		$insertData = [
			'wiki' => $wiki,
			'url' => $url,
			'title' => $title,
			'count' => $count
		];

		$sth->execute( $insertData );
	}

	if ( $db->commit() ) {
		break;
	}

	sleep( 1 );
}

curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PURGE' );
curl_setopt( $ch, CURLOPT_URL, "http://127.0.0.1:6081/" );
curl_exec( $ch );
$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
print "Purge: $code\n";
