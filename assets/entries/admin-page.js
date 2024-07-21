/**
 * WordPress dependencies
 */
import { setLocaleData } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

setLocaleData( { '': {} }, 'wp-mail-debugger' );

/**
 * Internal dependencies
 */
import App from './admin-page/app.js';
import Context from './admin-page/context';

domReady( () => {
	const containerEl = document.getElementById( 'wp-mail-debugger-root' );
	const isNetworkAdmin = containerEl.dataset[ 'is-network-admin' ] === '1';

	const root = createRoot( containerEl );
	root.render(
		<Context.Provider value={ { isNetworkAdmin } }>
			<App />
		</Context.Provider>
	);
} );

export * from './admin-page/api';
