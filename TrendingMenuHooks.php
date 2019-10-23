<?php

class TrendingMenuHooks {

	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModules( 'ext.trendingmenu' );
		return true;
	}

}
