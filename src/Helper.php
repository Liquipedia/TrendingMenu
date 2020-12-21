<?php

namespace Liquipedia\TrendingMenu;

use MediaWiki\MediaWikiServices;

class Helper {

	/**
	 * @return array
	 */
	public static function getWikiList() {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$dbr = wfGetDB( DB_REPLICA, [], $config->get( 'DBname' ) );
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
		$dbr = wfGetDB( DB_REPLICA, [], 'liquid-' );
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
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$dbw = wfGetDB( DB_MASTER, [], $config->get( 'DBname' ) );
		$dbw->query( 'TRUNCATE TABLE ' . $dbw->tableName( 'wiki_list' ), __METHOD__ );
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
		$dbw->insert( 'wiki_list', $toInsert );
	}

	/**
	 *
	 * @param string $wiki
	 */
	public static function add( $wiki ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$dbw = wfGetDB( DB_MASTER, [], $config->get( 'DBname' ) );
		$dbw->insert( 'wiki_list', $wiki );
	}

	/**
	 * @param string $wiki
	 *
	 * @return bool
	 */
	public static function exists( $wiki ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$dbr = wfGetDB( DB_REPLICA, [], $config->get( 'DBname' ) );
		$res = $dbr->select( 'wiki_list',
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
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$dbr = wfGetDB( DB_REPLICA, [], $config->get( 'DBname' ) );
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
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$dbw = wfGetDB( DB_MASTER, [], $config->get( 'DBname' ) );
		$dbw->delete( 'wiki_list', [ 'slug' => $slug ] );
	}

}
