<?php

class TrendingMenuHooks {
	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModuleScripts( 'ext.trendingmenu' );
		return true;
	}
}