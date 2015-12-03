<?php

/* includes from TLs codebase */
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../../public_html/includes/connect.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../../public_html/includes/functions.php');

$wgExtensionCredits['parserhook'][] = array(
								'name' => 'TrendingMenu',
								'author' =>'Alex Winkler',
								'url' => '',
								'description' => 'Shows most popular pages on the wiki.',
								'descriptionmsg' => "trendingmenu-desc",
								'version' => '1.0',
								'path' => __FILE__,
);

$wgHooks['SkinBuildSidebar'][] = 'fnTrendingMenu';

function fnTrendingMenu( $skin, &$bar ) {
	$trendingArticles = array ();
	global $wgScriptPath;
	$r = mysql_queryS ("SELECT * FROM wiki_hot WHERE page LIKE '%://wiki.teamliquid.net/".substr($wgScriptPath, 1)."/%' ORDER BY hits DESC LIMIT 5");
	while ($row = mysql_fetch_assoc ($r)) {
		$title = $row['title'];
		$title = str_replace ("_", " ", $title);
		$url = $row['page'];
		$trendingArticles[] = array (
			'title' => $title,
			'href' => $url
		);
	}
	
	$bar['TRENDING'] = $trendingArticles;
	return true;
}


?>