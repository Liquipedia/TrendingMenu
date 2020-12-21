( function( document, mw ) {
	var button = document.getElementById( 'wikilist-submit-button' );
	var p = document.getElementById( 'wikilist-button-click-notification' );
	button.addEventListener( 'click', function() {
		var wikiTypes = [ 'mainWiki', 'alphaWiki', 'preAlphaWiki' ];
		var json = { };
		wikiTypes.forEach( function( type ) {
			var wikiList = document.getElementById( type ).getElementsByTagName( 'li' );
			var totalWikis = wikiList.length;
			if ( totalWikis > 0 ) {
				json[ type ] = [ ];
				for ( var i = 0; i < totalWikis; i++ ) {
					json[ type ].push( { name: wikiList[ i ].innerText, slug: wikiList[ i ].getAttribute( 'slug-name' ) } );
				}
			}
		} );
		var api = new mw.Api();
		api.post( {
			action: 'updatewikilist',
			data: JSON.stringify( json )
		} ).done( function( data ) {
			p.innerText = data.updatewikilist.result;
			window.setTimeout( function() {
				location.reload();
			}, 2000 );
		} );
	} );
	window.addEventListener( 'load', function() {
		$( '#mainWiki, #alphaWiki, #preAlphaWiki' ).sortable( {
			connectWith: 'ul',
			placeholder: 'placeholder',
			delay: 150
		} )
			.disableSelection();
	} );
}( document, mw ) );
