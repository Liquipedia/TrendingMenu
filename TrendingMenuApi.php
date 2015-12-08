<?php

class TrendingMenuApi extends ApiBase {
	public function execute() {
                // Tell squids to cache
                $this->getMain()->setCacheMode( 'public' );
                // Set the squid & private cache time in seconds
                $this->getMain()->setCacheMaxAge( 300 );

		/* includes from TLs codebase */
		require_once ($_SERVER['DOCUMENT_ROOT'] . '/../../public_html/includes/connect.php');
		require_once ($_SERVER['DOCUMENT_ROOT'] . '/../../public_html/includes/functions.php');

		$trendingArticles = array ();
                global $wgScriptPath;
                $r = mysql_queryS ("SELECT * FROM wiki_hot WHERE wiki = '" . mysql_real_escape_string (substr($wgScriptPath, 1)) . "' ORDER BY hits DESC LIMIT 5");
                while ($row = mysql_fetch_assoc ($r)) {
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
			'api.php?action=trendingmenu&format=xml'
			=> 'trendingmenuapi-example'
		);
	}
}
