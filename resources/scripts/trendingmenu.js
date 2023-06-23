( function( window, document, mw ) {
	'use strict';
	const trendingmenu = {
		init: function() {
			if ( document.readyState === 'loading' ) {
				window.addEventListener( 'DOMContentLoaded', trendingmenu.run );
			} else {
				trendingmenu.run();
			}
		},
		run: function() {
			mw.loader.using( [ 'mediawiki.util', 'mediawiki.api' ] ).then( function() {
				const menuItem = document.getElementById( 'trending-pages-menu' );
				if ( menuItem !== null ) {
					const api = new mw.Api();
					api.get( {
						action: 'trendingmenu',
						uselang: 'content',
						format: 'json'
					} ).done( function( data ) {
						let html = '';
						for ( let i = 0; i < 5; i++ ) {
							if ( data.trendingmenu[ i ] ) {
								html += '<a class="dropdown-item" href="' + data.trendingmenu[ i ].href + '">' + data.trendingmenu[ i ].text + '</a>';
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
