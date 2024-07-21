/**
 * External dependencies
 */
import memize from 'memize';

/**
 * WordPress dependencies
 */
import { TabPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import HTMLTab from './html-tab';
import './style.css';

const getTabs = memize( ( email ) => {
	const tabs = [];

	if (
		email.headers[ 'Content-Type' ] &&
		email.headers[ 'Content-Type' ].includes( 'text/html' )
	) {
		tabs.push( {
			name: 'html',
			title: __( 'HTML', 'wp-mail-debugger' ),
			email,
		} );
	}

	tabs.push( {
		name: 'text',
		title: __( 'Plain Text', 'wp-mail-debugger' ),
		email,
	} );

	return tabs;
} );

function renderTab( tab ) {
	if ( tab.name === 'html' ) {
		return <HTMLTab email={ tab.email } />;
	}

	return (
		<div className="wmd-message-tab wmd-message-tab--text">
			<pre>{ tab.email.message.trim() }</pre>
		</div>
	);
}

function Message( { email } ) {
	return <TabPanel tabs={ getTabs( email ) }>{ renderTab }</TabPanel>;
}

export default Message;
