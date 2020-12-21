<?php

namespace Liquipedia\TrendingMenu;

use DatabaseUpdater;
use MediaWiki\MediaWikiServices;

class Hooks {

	/**
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModules( 'ext.trendingmenu' );
		return true;
	}

	/**
	 * @param Module $module
	 * @param User $user
	 * @param Message &$message
	 *
	 * @return bool
	 */
	public static function onApiCheckCanExecute( $module, $user, &$message ) {
		$moduleName = $module->getModuleName();
		if (
			$moduleName == 'updatewikilist' && !$user->isAllowed( 'edit-wikilist' )
		) {
			$message = 'updatewikilist-error-action-notallowed';
			return false;
		}
		return true;
	}

	/**
	 * @param DatabaseUpdater $updater
	 */
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$db = $updater->getDB();
		if ( !$db->tableExists( $config->get( 'DBname' ) . '.wiki_list', __METHOD__ ) ) {
			$updater->addExtensionTable( 'wiki_list', __DIR__ . '/../sql/wiki_list.sql' );
		}
	}

}
