<?php

namespace Liquipedia\Extension\TrendingMenu\Hooks;

use Config;
use MediaWiki\Api\Hook\ApiCheckCanExecuteHook;
use MediaWiki\Hook\BeforePageDisplayHook;

class MainHookHandler implements
	ApiCheckCanExecuteHook,
	BeforePageDisplayHook
{

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

}
