<?php

namespace Liquipedia\TrendingMenu;

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

}
