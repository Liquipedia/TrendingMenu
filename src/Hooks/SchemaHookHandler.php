<?php

namespace Liquipedia\Extension\TrendingMenu\Hooks;

use DatabaseUpdater;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;
use MediaWiki\MediaWikiServices;

class SchemaHookHandler implements
	LoadExtensionSchemaUpdatesHook
{

	/**
	 * @param DatabaseUpdater $updater
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$db = $updater->getDB();
		$config = MediaWikiServices::getInstance()->getMainConfig();
		if ( !$db->tableExists( $config->get( 'DBname' ) . '.wiki_list',
				__METHOD__ ) ) {
			$updater->addExtensionTable( 'wiki_list',
				__DIR__ . '/../sql/wiki_list.sql' );
		}
	}

}
