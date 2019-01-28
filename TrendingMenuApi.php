<?php

class TrendingMenuApi extends ApiBase {

	public function execute() {
		global $TL_DB;
		// Tell squids to cache
		$this->getMain()->setCacheMode( 'public' );
		// Set the squid & private cache time in seconds
		$this->getMain()->setCacheMaxAge( 300 );
		$trendingArticles = [];

		$dbr = wfGetDB( DB_REPLICA, '', $TL_DB );
		$res = $dbr->select( 'wiki_hot', '*', [ 'wiki' => substr( $this->getConfig()->get( 'ScriptPath' ), 1 ) ], __METHOD__, [ 'ORDER BY' => 'hits DESC', 'LIMIT' => 5 ] );
		if ( $dbr->numRows( $res ) ) {
			while ( $row = $res->fetchObject() ) {
				$trendingArticles[] = [
					'text' => utf8_decode( htmlspecialchars( strip_tags( str_replace( '_', ' ', $row->title ) ) ) ),
					'href' => utf8_decode( htmlspecialchars( $row->page ) ),
				];
			}
		}

		$this->getResult()->addValue( null, $this->getModuleName(), $trendingArticles );

		return true;
	}

	public function getDescription() {
		return wfMessage( 'trendingmenu-shortdesc' )->text();
	}

	public function getAllowedParams() {
		return parent::getAllowedParams();
	}

	public function getParamDescription() {
		return parent::getParamDescription();
	}

	public function getExamplesMessages() {
		return array(
			'action=trendingmenu&format=json' => 'trendingmenuapi-example'
		);
	}

}
