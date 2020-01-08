<?php

namespace Liquipedia\TrendingMenu;

class Hooks {

	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModules( 'ext.trendingmenu' );
		return true;
	}

}
