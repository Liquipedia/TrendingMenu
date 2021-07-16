<?php

namespace Liquipedia\Extension\TrendingMenu\Hooks;

use Config;
use DatabaseUpdater;
use MediaWiki\Api\Hook\ApiCheckCanExecuteHook;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;
use MediaWiki\MediaWikiServices;

class HookHandler
	implements ApiCheckCanExecuteHook,
	BeforePageDisplayHook,
	LoadExtensionSchemaUpdatesHook {

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @param Config $config
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		$out->addModules( 'ext.trendingmenu' );
	}

	/**
	 * @param Module $module
	 * @param User $user
	 * @param Message &$message
	 *
	 * @return bool
	 */
	public function onApiCheckCanExecute( $module, $user, &$message ) {
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
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$db = $updater->getDB();
		if ( !$db->tableExists( $config->get( 'DBname' ) . '.wiki_list', __METHOD__ ) ) {
			$updater->addExtensionTable( 'wiki_list', __DIR__ . '/../sql/wiki_list.sql' );
		}
	}

}
