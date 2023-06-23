( function( document, mw ) {
	const button = document.getElementById( 'wikilist-submit-button' );
	const p = document.getElementById( 'wikilist-button-click-notification' );
	button.addEventListener( 'click', function() {
		const wikiTypes = [ 'mainWiki', 'alphaWiki', 'preAlphaWiki' ];
		const json = { };
		wikiTypes.forEach( function( type ) {
			const wikiList = document.getElementById( type ).getElementsByTagName( 'li' );
			const totalWikis = wikiList.length;
			if ( totalWikis > 0 ) {
				json[ type ] = [ ];
				for ( let i = 0; i < totalWikis; i++ ) {
					json[ type ].push( { name: wikiList[ i ].innerText, slug: wikiList[ i ].getAttribute( 'slug-name' ) } );
				}
			}
		} );
		const api = new mw.Api();
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
