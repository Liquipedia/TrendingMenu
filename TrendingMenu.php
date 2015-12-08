<?php

$wgExtensionCredits['api'][] = array(
								'name' => 'TrendingMenu',
								'author' =>'Alex Winkler',
								'url' => 'http://FO-nTTaX.de',
								'description' => 'Shows most popular pages on the wiki.',
								'descriptionmsg' => "trendingmenu-desc",
								'version' => '1.0',
								'path' => __FILE__,
);

$wgAPIModules['trendingmenu'] = 'TrendingMenuApi';

$wgAutoloadClasses['TrendingMenuApi'] = __DIR__ . '/TrendingMenuApi.php';

?>
