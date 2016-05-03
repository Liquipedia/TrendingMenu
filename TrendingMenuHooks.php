<?php

class TrendingMenuHooks {
	public static function beforePageDisplay( $out, $skin ) {
		$out->addModuleScripts( 'ext.trendingmenu' );
		return true;
	}
}