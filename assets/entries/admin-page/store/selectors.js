/**
 * WordPress dependencies
 */
import { createRegistrySelector } from '@wordpress/data';
import { CORE_STORE } from '../../shared/constants';

/**
 * Get the currently being viewed email.
 *
 * @param {Object} state The state object.
 * @return {string} The email id. Empty string if on list view.
 */
export function getCurrentEmail( state ) {
	return state.currentEmail;
}

/**
 * Get the search term.
 *
 * @param {Object} state The state object.
 * @return {string} The search term. Empty string if no search.
 */
export function getSearchTerm( state ) {
	return state.searchTerm;
}

/**
 * Checks if the search view is enabled.
 *
 * @param {Object} state The state object.
 * @return {boolean} Whether search is enabled.
 */
export function isSearchEnabled( state ) {
	return state.searchEnabled;
}

/**
 * Get the active query id.
 *
 * @param {Object} state The state object.
 * @return {string} Query id.
 */
export function getQueryId( state ) {
	if ( isSearchEnabled( state ) ) {
		return 'search';
	}

	return 'main';
}

/**
 * Get the emails for the main application view.
 *
 * @return {Array<Object>} List of email objects.
 */
export const getEmails = createRegistrySelector(
	( select ) => () => select( CORE_STORE ).getQueryResults( 'main' )
);
