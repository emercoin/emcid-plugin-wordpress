(function( $ ) {
	'use strict'
    $( document ).on( 'click', '.js-emcl', function( e ) {
        e.preventDefault()
        window.open(emcl.authPage + '?client_id=' + emcl.appId + '&redirect_uri=' + emcl.redirect + '&response_type=code', '_self', null)
    })
})( jQuery )
