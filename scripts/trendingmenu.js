( function( window, document, mw ) {
	'use strict';
	var trendingmenu = {
		init: function() {
			if ( document.readyState === 'loading' ) {
				window.addEventListener( 'DOMContentLoaded', trendingmenu.run );
			} else {
				trendingmenu.run();
			}
		},
		run: function() {
			mw.loader.using( [ 'mediawiki.util', 'mediawiki.api' ] ).then( function() {
				var menuItem = document.getElementById( 'trending-pages-menu' );
				if ( menuItem !== null ) {
					var api = new mw.Api();
					api.get( {
						action: 'trendingmenu',
						uselang: 'content',
						format: 'json'
					} ).done( function( data ) {
						var html = '';
						for ( var i = 0; i < 5; i++ ) {
							var skin = mw.config.get( 'skin' );
							if ( data.trendingmenu[i] ) {
								if ( skin === 'bruinen' ) {
									html += '<a class="dropdown-item" href="' + data.trendingmenu[i].href + '">' + data.trendingmenu[i].text + '</a>';
								} else {
									html += '<li><a href="' + data.trendingmenu[i].href + '">' + data.trendingmenu[i].text + '</a></li>';
								}
							}
						}
						menuItem.innerHTML = html;
					} );
				}
			} );
		}
	};
	trendingmenu.init();

}( window, document, mw ) );
