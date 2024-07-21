/**
 * External dependencies
 */
import InfiniteScroll from 'react-infinite-scroll-component';

/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { Fragment, useContext } from '@wordpress/element';

/**
 * Internal dependencies
 */
import EmailListItem from '../email-list-item';
import Search from '../search';
import { CORE_STORE, ADMIN_PAGE_STORE } from '../../../shared/constants';
import Context from '../../context';

function EmailList() {
	const { isNetworkAdmin } = useContext( Context );
	const queryId = useSelect( ( select ) =>
		select( ADMIN_PAGE_STORE ).getQueryId()
	);
	const { emails, hasMore } = useSelect(
		( select ) => {
			return {
				emails:
					( queryId === 'main'
						? select( ADMIN_PAGE_STORE ).getEmails( isNetworkAdmin )
						: select( CORE_STORE ).getQueryResults( queryId ) ) ||
					[],
				hasMore: !! select( CORE_STORE ).getQueryHeaderLink(
					'main',
					'next'
				),
			};
		},
		[ queryId, isNetworkAdmin ]
	);
	const fetchQueryNextPage = useDispatch( CORE_STORE ).fetchQueryNextPage;
	const fetchMore = () => fetchQueryNextPage( queryId );

	return (
		<Fragment>
			<Search />
			<InfiniteScroll
				dataLength={ emails.length }
				next={ fetchMore }
				hasMore={ hasMore }
			>
				<ul>
					{ emails.map( ( email ) => (
						<EmailListItem email={ email } key={ email.uuid } />
					) ) }
				</ul>
			</InfiniteScroll>
		</Fragment>
	);
}

export default EmailList;
