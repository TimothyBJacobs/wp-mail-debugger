/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { SlotFillProvider } from '@wordpress/components';
import { PluginArea } from '@wordpress/plugins';

/**
 * Internal dependencies
 */
import Header from './components/header';
import EmailList from './components/email-list';
import Detail from './components/detail';
import { ADMIN_PAGE_STORE } from '../shared/constants';
import './store';
import './style.css';

function App( { currentEmail } ) {
	return (
		<SlotFillProvider>
			<Header />
			<div
				className={ classnames(
					'wmd-body',
					currentEmail ? 'wmd-body--detail' : 'wmd-body--list'
				) }
			>
				{ currentEmail ? (
					<Detail id={ currentEmail } />
				) : (
					<EmailList />
				) }
			</div>
			<PluginArea />
		</SlotFillProvider>
	);
}

export default compose( [
	withSelect( ( select ) => ( {
		currentEmail: select( ADMIN_PAGE_STORE ).getCurrentEmail(),
	} ) ),
] )( App );
