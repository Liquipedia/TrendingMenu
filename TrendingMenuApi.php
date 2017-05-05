<?php

class TrendingMenuApi extends ApiBase {
	public static function get_db_object() {
		global $wgDBtype,
			$wgDBserver,
			$TL_DB,
			$wgDBuser,
			$wgDBpassword;
		$db = null;
		try {
			$db = new PDO( $wgDBtype . ':host=' . $wgDBserver. ';dbname=' . $TL_DB,
				$wgDBuser, $wgDBpassword );
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$db->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
			$db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		} catch( PDOException $e ) {
			// echo "Connection Error: " . $e->getMessage();
		}
		return $db;
	}
	public function execute() {
		global $wgScriptPath;
		// Tell squids to cache
		$this->getMain()->setCacheMode( 'public' );
		// Set the squid & private cache time in seconds
		$this->getMain()->setCacheMaxAge( 300 );
		$trendingArticles = array ();

		$db = self::get_db_object();
		if( $db == null ) {
			return null;
		}
		$pdostatement = $db->prepare( "SELECT * FROM `wiki_hot` WHERE `wiki` = :wiki ORDER BY hits DESC LIMIT 5" );
		$pdostatement->execute( ['wiki' => substr($wgScriptPath, 1)] );
		$rows = $pdostatement->fetchAll();

		foreach( $rows as $row ) {
			$title = $row['title'];
			$title = str_replace ("_", " ", $title);
			$url = $row['page'];
			$trendingArticles[] = array (
				'text' => $title,
				'href' => $url
			);
		}

		$this->getResult()->addValue( null, $this->getModuleName(), $trendingArticles );

		return true;
	}

	public function getDescription() {
		return 'trendingmenu-shortdesc';
	}

	public function getAllowedParams() {
		return parent::getAllowedParams();
	}

	public function getParamDescription() {
		return parent::getParamDescription();
	}

	public function getExamplesMessages() {
		return array(
			'action=trendingmenu&format=xml'
			=> 'trendingmenuapi-example'
		);
	}
}
