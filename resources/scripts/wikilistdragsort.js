( function( document, mw ) {
	const button = document.getElementById( 'wikilist-submit-button' );
	const p = document.getElementById( 'wikilist-button-click-notification' );
	button.addEventListener( 'click', () => {
		const wikiTypes = [ 'mainWiki', 'alphaWiki', 'preAlphaWiki' ];
		const json = { };
		wikiTypes.forEach( ( type ) => {
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
		} ).then( ( data ) => {
			p.innerText = data.updatewikilist.result;
			window.setTimeout( () => {
				location.reload();
			}, 2000 );
		} );
	} );
	window.addEventListener( 'load', () => {
		// The drag-and-drop wiki list intentionally uses the jquery.ui ResourceLoader module.
		// eslint-disable-next-line no-jquery/no-jquery-ui
		$( '#mainWiki, #alphaWiki, #preAlphaWiki' ).sortable( {
			connectWith: 'ul',
			placeholder: 'placeholder',
			delay: 150
		} )
			.disableSelection();
	} );
}( document, mw ) );
