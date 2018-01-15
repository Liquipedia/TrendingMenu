<?php

class TrendingMenuApi extends ApiBase {
	public function execute() {
		global $wgScriptPath, $TL_DB;
		// Tell squids to cache
		$this->getMain()->setCacheMode( 'public' );
		// Set the squid & private cache time in seconds
		$this->getMain()->setCacheMaxAge( 300 );
		$trendingArticles = array();

		$dbr = wfGetDB( DB_REPLICA, '', $TL_DB );
		$res = $dbr->select( 'wiki_hot', '*', array( 'wiki' => substr( $wgScriptPath, 1 ) ), __METHOD__, array( 'order' => 'hits DESC', 'limit' => 5 ) );
		if( $dbr->numRows( $res ) ) {
			while( $row = $res->fetchObject() ) {
				$trendingArticles[] = array(
					'text' => str_replace( '_', ' ', $row->title ),
					'href' => $row->page,
				);
			}
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
			'action=trendingmenu&format=xml' => 'trendingmenuapi-example'
		);
	}
}
