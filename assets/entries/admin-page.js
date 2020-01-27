/**
 * WordPress dependencies
 */
import { setLocaleData } from '@wordpress/i18n';
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

setLocaleData( { '': {} }, 'wp-mail-debugger' );

/**
 * Internal dependencies
 */
import App from './admin-page/app.js';

domReady( () => {
	const containerEl = document.getElementById( 'wp-mail-debugger-root' );

	return render( <App />, containerEl );
} );

export * from './admin-page/api';
