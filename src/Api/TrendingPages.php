<?php

namespace Liquipedia\Extension\TrendingMenu\Api;

use ApiBase;
use MediaWiki\MediaWikiServices;

class TrendingPages extends ApiBase {

	public function execute() {
		// Tell squids to cache
		$this->getMain()->setCacheMode( 'public' );
		// Set the squid & private cache time in seconds
		$this->getMain()->setCacheMaxAge( 300 );
		$trendingArticles = [];

		$wiki = substr( $this->getConfig()->get( 'ScriptPath' ), 1 );
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$loadBalancer = MediaWikiServices::getInstance()->getDBLoadBalancer();
		$dbr = $loadBalancer->getConnection( DB_REPLICA, [], $config->get( 'DBname' ) );
		$res = $dbr->select(
			'wiki_hot',
			'*',
			[
				'wiki' => $wiki
			],
			 __METHOD__,
			[
				'ORDER BY' => 'hits DESC',
				'LIMIT' => 10
			]
		);
		if ( $res->numRows() ) {
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
