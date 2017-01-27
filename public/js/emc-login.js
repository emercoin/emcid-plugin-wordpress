(function( $ ) {
	'use strict'
    $( document ).on( 'click', '.js-emcl', function( e ) {
        e.preventDefault()
        window.open('https://id.emercoin.net/oauth/v2/auth?client_id=' + emcl.appId + '&redirect_uri=' + emcl.redirect + '&response_type=code', '_self', null)
    })
})( jQuery )
