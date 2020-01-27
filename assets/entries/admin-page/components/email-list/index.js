/**
 * External dependencies
 */
import InfiniteScroll from 'react-infinite-scroll-component';

/**
 * WordPress dependencies
 */
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';

/**
 * Internal dependencies
 */
import EmailListItem from '../email-list-item';
import Search from '../search';
import { CORE_STORE, ADMIN_PAGE_STORE } from '../../../shared/constants';

function EmailList( { emails, hasMore, fetchMore } ) {
	return (
		<Fragment>
			<Search />
			<InfiniteScroll dataLength={ emails.length } next={ fetchMore } hasMore={ hasMore }>
				<ul>
					{ emails.map( ( email ) => (
						<EmailListItem email={ email } key={ email.uuid } />
					) ) }
				</ul>
			</InfiniteScroll>
		</Fragment>
	);
}

export default compose( [
	withSelect( ( select ) => {
		const queryId = select( ADMIN_PAGE_STORE ).getQueryId();
		let emails;

		if ( queryId === 'main' ) {
			emails = select( ADMIN_PAGE_STORE ).getEmails();
		} else {
			emails = select( CORE_STORE ).getQueryResults( queryId );
		}

		return {
			emails: emails || [],
			hasMore: !! select( CORE_STORE ).getQueryHeaderLink( 'main', 'next' ),
		};
	} ),
	withDispatch( ( dispatch ) => ( {
		fetchMore() {
			return dispatch( CORE_STORE ).fetchQueryNextPage( 'main' );
		},
	} ) ),
] )( EmailList );
