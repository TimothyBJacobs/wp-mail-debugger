/**
 * WordPress dependencies
 */
import { withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { ADMIN_PAGE_STORE } from '../../../shared/constants';
import { formatAddress } from '../../../shared/utils';
import SentAt from '../sent-at';
import './style.css';

function EmailListItem( { email, view } ) {
	return (
		<li className="wmd-email-list-item">
			<h3 className="wmd-email-list-item__subject">
				<a href="#" onClick={ view }>
					{ email.subject }
				</a>
			</h3>

			<SentAt email={ email } />
			<p className="wmd-email-list-item__to">
				{ email.to.map( formatAddress ).join( ', ' ) }
			</p>
		</li>
	);
}

export default compose( [
	withDispatch( ( dispatch, ownProps ) => ( {
		view() {
			return dispatch( ADMIN_PAGE_STORE ).viewEmail( ownProps.email.uuid );
		},
	} ) ),
] )( EmailListItem );
