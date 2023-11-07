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
		$tables = [ 'wiki_list', 'wiki_hot', 'wiki_hits' ];
		foreach ( $tables as $table ) {
			if ( !$db->tableExists( $config->get( 'DBname' ) . '.' . $table,
				__METHOD__ ) ) {
				$updater->addExtensionTable( $table,
					__DIR__ . '/../../sql/' . $table . '.sql' );
			}
		}
	}

}
