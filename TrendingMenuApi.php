<?php

class TrendingMenuApi extends ApiBase {
	public function execute() {
                // Tell squids to cache
                $this->getMain()->setCacheMode( 'public' );
                // Set the squid & private cache time in seconds
                $this->getMain()->setCacheMaxAge( 300 );

		$trendingArticles = array ();

		/* includes from TLs codebase */

		global $wgScriptPath;
		global $TL_DB;

		mysql_select_db ($TL_DB);
                $r = mysql_query ("SELECT * FROM wiki_hot WHERE wiki = '" . mysql_real_escape_string (substr($wgScriptPath, 1)) . "' ORDER BY hits DESC LIMIT 5");
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
