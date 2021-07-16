<?php

namespace Liquipedia\Extension\TrendingMenu\Api;

use ApiBase;

class TrendingPages extends ApiBase {

	public function execute() {
		global $TL_DB; // phpcs:ignore
		// Tell squids to cache
		$this->getMain()->setCacheMode( 'public' );
		// Set the squid & private cache time in seconds
		$this->getMain()->setCacheMaxAge( 300 );
		$trendingArticles = [];

		$wiki = substr( $this->getConfig()->get( 'ScriptPath' ), 1 );

		$dbr = wfGetDB( DB_REPLICA, '', $TL_DB );
		$res = $dbr->select(
			'wiki_hot', '*', [
				'wiki' => $wiki
			], __METHOD__, [
				'ORDER BY' => 'hits DESC',
				'LIMIT' => 5
			]
		);
		if ( $dbr->numRows( $res ) ) {
			foreach ( $res as $row ) {
				$trendingArticles[] = [
					'text' => htmlspecialchars( $row->title ),
					'href' => htmlspecialchars( '/' . $wiki . '/' . $row->page ),
				];
			}
		}

		$this->getResult()->addValue( null, $this->getModuleName(), $trendingArticles );

		return true;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->msg( 'trendingmenu-shortdesc' )->text();
	}

	/**
	 * @return array
	 */
	public function getAllowedParams() {
		return parent::getAllowedParams();
	}

	/**
	 * @return string
	 */
	public function getParamDescription() {
		return parent::getParamDescription();
	}

	/**
	 * @return array
	 */
	public function getExamplesMessages() {
		return [
			'action=trendingmenu&format=json' => 'trendingmenuapi-example'
		];
	}

}
