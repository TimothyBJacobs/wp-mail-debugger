/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withSelect, withDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { ADMIN_PAGE_STORE, CORE_STORE } from '../../../shared/constants';
import Settings from '../settings';
import './style.css';

function Header( {
	queryId,
	refresh,
	isRefreshing,
	emptyInbox,
	isEmptyingInbox,
	numFound,
} ) {
	return (
		<div className="wmd-header">
			<div className="wmd-header__actions">
				<div className="wmd-header__actions-container--left">
					<Button
						icon="update"
						onClick={ refresh }
						isBusy={ isRefreshing }
						disabled={ queryId !== 'main' }
					>
						{ __( 'Refresh', 'wp-mail-debugger' ) }
					</Button>
					<Button
						icon="trash"
						onClick={ emptyInbox }
						isBusy={ isEmptyingInbox }
					>
						{ __( 'Empty Inbox', 'wp-mail-debugger' ) }
					</Button>
				</div>
				<div className="wmd-header__actions-container--right">
					<Settings />
				</div>
			</div>
			<div className="wmd-header__title">
				<h1>{ __( 'WP Mail Debugger', 'wp-mail-debugger' ) }</h1>
				<span className="wmd-header__title-found-count">
					{ numFound }
				</span>
			</div>
		</div>
	);
}

export default compose( [
	withSelect( ( select ) => ( {
		queryId: select( ADMIN_PAGE_STORE ).getQueryId(),
		numFound: select( CORE_STORE ).getQueryHeader(
			select( ADMIN_PAGE_STORE ).getQueryId(),
			'x-wp-total'
		),
		isRefreshing: select( CORE_STORE ).isQuerying( 'main' ),
		isEmptyingInbox: select( CORE_STORE ).isEmptyingInbox(),
	} ) ),
	withDispatch( ( dispatch ) => ( {
		emptyInbox: dispatch( CORE_STORE ).emptyInbox,
		refresh() {
			dispatch( CORE_STORE ).query( 'main', { context: 'embed' } );
		},
	} ) ),
] )( Header );
