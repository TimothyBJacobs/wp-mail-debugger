/**
 * External dependencies
 */
import createSelector from 'rememo';
import { get, find } from 'lodash';

/**
 * Gets the items returned by a query.
 *
 * @param {Object} state   State object.
 * @param {string} queryId Query id.
 * @return {Array<Object>}
 */
export const getQueryResults = createSelector(
	( state, queryId ) => {
		const ids = get( state, [ 'queries', queryId, 'ids' ], [] );
		const byId = state.byId;

		const length = ids.length;
		const items = new Array( length );
		let index = -1;

		while ( ++index < length ) {
			const entry = byId[ ids[ index ] ];

			if ( entry ) {
				items[ index ] = entry.email;
			}
		}

		return items;
	},
	( state, queryId ) => [ state.queries[ queryId ], state.byId ]
);

/**
 * Gets the link header from a query result.
 *
 * @param {Object} state   State object.
 * @param {string} queryId Query id.
 * @param {string} rel     Rel to search for.
 * @return {{link: string, rel: string}} Link object or undefined if not found.
 */
export function getQueryHeaderLink( state, queryId, rel ) {
	return find( get( state, [ 'queries', queryId, 'links' ], [] ), { rel } );
}

/**
 * Get a response header from a query.
 *
 * @param {Object} state   State object.
 * @param {string} queryId Query id.
 * @param {string} header  Normalized header name.
 * @return {string|undefined} The header value, or undefined if it does not exist.
 */
export function getQueryHeader( state, queryId, header ) {
	return get( state, [ 'queries', queryId, 'headers', header ] );
}

/**
 * Get an email object.
 *
 * @param {Object} state   The state object.
 * @param {string} id      The email id.
 * @param {string} context The context to request the item with. Defaults to view.
 * @return {Object|undefined} The email object or undefined if it could not be found.
 */
export function getEmail( state, id, context = 'view' ) {
	const entry = state.byId[ id ];

	if ( ! entry || entry.context !== context ) {
		return undefined;
	}

	return entry.email;
}

/**
 * Get the plugin's settings.
 *
 * @param {Object} state The state object.
 * @return {Object} The settings object.
 */
export function getSettings( state ) {
	return state.settings;
}

/**
 * Checks if a query is in progress.
 *
 * @param {Object} state   The state object.
 * @param {string} queryId The query id.
 * @return {boolean} Whether the query is in progress.
 */
export function isQuerying( state, queryId ) {
	return state.querying.includes( queryId );
}

/**
 * Checks if an email is being fetched.
 *
 * @param {Object} state The state object.
 * @param {string} id    The email id.
 * @return {boolean} Whether the query is in progress.
 */
export function isFetching( state, id ) {
	return state.fetching.includes( id );
}

/**
 * Checks if an email is being sent.
 * @param {Object} state The state object.
 * @param {string} id    The email id
 * @return {boolean} Whether the email is being sent.
 */
export function isSending( state, id ) {
	return state.sending.includes( id );
}

/**
 * Checks if an email is being deleted.
 *
 * @param {Object} state The state object.
 * @param {string} id    The email id.
 * @return {boolean} Whether the query is in progress.
 */
export function isDeleting( state, id ) {
	return state.deleting.includes( id );
}

/**
 * Checks if the inbox is being emptied.
 *
 * @param {Object} state The state object.
 * @return {boolean} Whether the query is in progress.
 */
export function isEmptyingInbox( state ) {
	return state.emptyingInbox;
}
