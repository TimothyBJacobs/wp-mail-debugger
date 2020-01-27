/**
 * Internal dependencies
 */
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import DetailActions from '../detail-actions';
import EmailMain from '../email-main';
import EmailSidebar from '../email-sidebar';
import { CORE_STORE } from '../../../shared/constants';
import './style.css';

function Detail( { email } ) {
	return email ? (
		<div className="wmd-detail">
			<DetailActions email={ email } />
			<div className="wmd-detail-body">
				<EmailMain email={ email } />
				<EmailSidebar email={ email } />
			</div>
		</div>
	) : null;
}

export default compose( [
	withSelect( ( select, { id } ) => ( {
		email: select( CORE_STORE ).getEmail( id ),
	} ) ),
] )( Detail );
