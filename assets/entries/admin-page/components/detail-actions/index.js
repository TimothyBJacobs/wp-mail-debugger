/**
 * WordPress dependencies
 */
import { IconButton } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withSelect, withDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ADMIN_PAGE_STORE, CORE_STORE } from '../../../shared/constants';
import './style.css';

function DetailActions( { viewList, deleteEmail, isDeleting } ) {
	return (
		<div className="wmd-detail-actions">
			<div className="wmd-detail-actions__container--left">
				<IconButton isLink icon="arrow-left-alt2" onClick={ viewList }>
					{ __( 'Back to Messages', 'wp-mail-debugger' ) }
				</IconButton>
			</div>
			<div className="wmd-detail-actions__container--right">
				<IconButton icon="trash" className="wmd-detail-actions--trash" onClick={ deleteEmail }
					isBusy={ isDeleting }>
					{ __( 'Delete', 'wp-mail-debugger' ) }
				</IconButton>
			</div>
		</div>
	);
}

export default compose( [
	withSelect( ( select, { email } ) => ( {
		isDeleting: select( CORE_STORE ).isDeleting( email.uuid ),
	} ) ),
	withDispatch( ( dispatch, { email } ) => ( {
		viewList: dispatch( ADMIN_PAGE_STORE ).viewList,
		deleteEmail() {
			return dispatch( CORE_STORE ).deleteEmail( email.uuid )
				.then( () => dispatch( ADMIN_PAGE_STORE ).viewList() );
		},
	} ) ),
] )( DetailActions );
