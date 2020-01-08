<?php

namespace Liquipedia\TrendingMenu;

use ApiBase;

class Api extends ApiBase {

	public function execute() {
		global $TL_DB;
		// Tell squids to cache
		$this->getMain()->setCacheMode( 'public' );
		// Set the squid & private cache time in seconds
		$this->getMain()->setCacheMaxAge( 300 );
		$trendingArticles = [];

		$wiki = substr( $this->getConfig()->get( 'ScriptPath' ), 1 );

		$dbr = wfGetDB( DB_REPLICA, '', $TL_DB );
		$res = $dbr->select( 'wiki_hot', '*', [ 'wiki' => $wiki ], __METHOD__, [ 'ORDER BY' => 'hits DESC', 'LIMIT' => 5 ] );
		if ( $dbr->numRows( $res ) ) {
			while ( $row = $res->fetchObject() ) {
				$trendingArticles[] = [
					'text' => htmlspecialchars( $row->title ),
					'href' => htmlspecialchars( "/$wiki/" . $row->page ),
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
		return [
			'action=trendingmenu&format=json' => 'trendingmenuapi-example'
		];
	}

}
