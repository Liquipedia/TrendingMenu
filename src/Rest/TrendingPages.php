<?php

namespace Liquipedia\Extension\TrendingMenu\Rest;

use Config;
use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use Wikimedia\Rdbms\LBFactory;

class TrendingPages extends SimpleHandler {

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var LBFactory
	 */
	private $loadBalancerFactory;

	/**
	 * @param Config $config
	 * @param LBFactory $loadBalancerFactory
	 */
	public function __construct(
		Config $config,
		LBFactory $loadBalancerFactory
	) {
		$this->config = $config;
		$this->loadBalancerFactory = $loadBalancerFactory;
	}

	/**
	 * @return Response
	 */
	public function run(): Response {
		$wiki = substr( $this->config->get( 'ScriptPath' ), 1 );

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

		$trendingArticles = [];
		if ( $dbr->numRows( $res ) ) {
			foreach ( $res as $row ) {
				$trendingArticles[] = [
					'text' => htmlspecialchars( $row->title ),
					'href' => htmlspecialchars( '/' . $wiki . '/' . $row->page ),
				];
			}
		}

		$response = $this->getResponseFactory()->createJson( $trendingArticles );

		// Cache this
		$response->setHeader( 'Cache-Control', 'public, max-age=300, s-maxage=300' );

		return $response;
	}

	/**
	 * @return bool
	 */
	public function needsWriteAccess() {
		return false;
	}

}
