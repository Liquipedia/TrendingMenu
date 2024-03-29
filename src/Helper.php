<?php

namespace Liquipedia\Extension\TrendingMenu;

use MediaWiki\MediaWikiServices;

class Helper {

	/**
	 * @return array
	 */
	public static function getWikiList() {
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$loadBalancer = $services->getDBLoadBalancer();
		$dbr = $loadBalancer->getConnection( DB_REPLICA,
			[],
			$config->get( 'DBname' ) );
		$res = $dbr->select(
			'wiki_list',
			[
				'*',
			]
		);
		$output = [
			'mainWiki' => [
				'name' => 'Main Wiki'
			],
			'alphaWiki' => [
				'name' => 'Alpha Wikis'
			],
			'preAlphaWiki' => [
				'name' => 'Pre Alpha Wikis'
			]
		];
		foreach ( $res as $row ) {
			$output[ $row->type ][ 'items' ][] = [
				'title' => $row->wiki,
				'slug' => $row->slug
			];
		}
		return $output;
	}

	/**
	 * @return array
	 */
	public static function getWikiHotList() {
		$services = MediaWikiServices::getInstance();
		$loadBalancer = $services->getDBLoadBalancer();
		$config = $services->getMainConfig();
		$dbr = $loadBalancer->getConnection( DB_REPLICA, [], $config->get( 'DBname' ) );
		$res = $dbr->select(
			'wiki_hot',
			[
				'*',
			],
			[],
			__METHOD__,
			[ 'ORDER BY' => 'hits DESC' ]
		);
		$output = [];
		foreach ( $res as $row ) {
			if ( isset( $output[ $row->wiki ] ) && count( $output[ $row->wiki ] ) === 5 ) {
				continue;
			}
			$output[ $row->wiki ][] = [
				'title' => $row->title,
				'page' => $row->page
			];
		}
		return $output;
	}

	/**
	 *
	 * @param string $jsonData
	 */
	public static function update( $jsonData ) {
		$data = json_decode( $jsonData );
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$loadBalancer = $services->getDBLoadBalancer();
		$dbw = $loadBalancer->getConnection(
			DB_PRIMARY,
			[],
			$config->get( 'DBname' )
		);
		$dbw->query(
			'TRUNCATE TABLE ' . $dbw->tableName( 'wiki_list' ),
			__METHOD__
		);
		$toInsert = [];
		foreach ( $data as $type => $details ) {
			foreach ( $details as $wiki ) {
				$toInsert[] = [
					'wiki' => $wiki->name,
					'slug' => $wiki->slug,
					'type' => $type
				];
			}
		}
		$dbw->insert( 'wiki_list',
			$toInsert );
	}

	/**
	 *
	 * @param string $wiki
	 */
	public static function add( $wiki ) {
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$loadBalancer = $services->getDBLoadBalancer();
		$dbw = $loadBalancer->getConnection(
			DB_PRIMARY,
			[],
			$config->get( 'DBname' )
		);
		$dbw->insert(
			'wiki_list',
			$wiki
		);
	}

	/**
	 * @param string $wiki
	 *
	 * @return bool
	 */
	public static function exists( $wiki ) {
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$loadBalancer = $services->getDBLoadBalancer();
		$dbr = $loadBalancer->getConnection(
			DB_REPLICA,
			[],
			$config->get( 'DBname' )
		);
		$res = $dbr->select(
				'wiki_list',
				'1',
				[
					'wiki' => $wiki
				]
			)->fetchRow();
		if ( $res ) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return array
	 */
	public static function getWikiNamesForDropList() {
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$loadBalancer = $services->getDBLoadBalancer();
		$dbr = $loadBalancer->getConnection( DB_REPLICA,
			[],
			$config->get( 'DBname' ) );
		$res = $dbr->select(
			'wiki_list',
			[
				'*',
			]
		);
		$wikis = [];
		foreach ( $res as $row ) {
			$wikis += [
				$row->wiki => $row->slug
			];
		}
		return $wikis;
	}

	/**
	 *
	 * @param string $slug
	 */
	public static function delete( $slug ) {
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$loadBalancer = $services->getDBLoadBalancer();
		$dbw = $loadBalancer->getConnection( DB_PRIMARY,
			[],
			$config->get( 'DBname' ) );
		$dbw->delete( 'wiki_list',
			[ 'slug' => $slug ] );
	}

}
