/**
 * External dependencies
 */
import { get, map, fromPairs, omit } from 'lodash';
import { parse } from 'li';

/**
 * Internal dependencies
 */
import {
	START_QUERY,
	FINISH_QUERY,
	FAILED_QUERY,
	START_FETCH_EMAIL,
	FINISH_FETCH_EMAIL,
	FAILED_FETCH_EMAIL,
	START_EMPTY_INBOX,
	FINISH_EMPTY_INBOX,
	FAILED_EMPTY_INBOX,
	START_DELETE_EMAIL,
	FINISH_DELETE_EMAIL,
	FAILED_DELETE_EMAIL,
	RECEIVE_EMAIL,
	RECEIVE_QUERY,
	RECEIVE_SETTINGS,
	START_SEND_EMAIL,
	FINISH_SEND_EMAIL,
	FAILED_SEND_EMAIL,
} from './actions';

const DEFAULT_STATE = {
	// Object of email ids to their full object
	byId: {},
	// Query details
	queries: {},
	// List of query ids being queried
	querying: [],
	// List of email ids of items that are being fetched
	fetching: [],
	// List of email ids of items that are being deleted
	deleting: [],
	// List of email ids of items that are being sent
	sending: [],
	// Is the inbox currently being updated
	emptyingInbox: false,
	// Settings object
	settings: {},
};

export default function reducer( state = DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case RECEIVE_QUERY:
			return {
				...state,
				queries: {
					...state.queries,
					[ action.queryId ]: {
						ids:
							action.mode === 'replace'
								? map( action.items, 'uuid' )
								: [
										...get(
											state,
											[
												'queries',
												action.queryId,
												'ids',
											],
											[]
										),
										...map( action.items, 'uuid' ),
								  ],
						headers: fromPairs(
							Array.from( action.response.headers.entries() )
						),
						links: parse( action.response.headers.get( 'link' ), {
							extended: true,
						} ).map( ( link ) => ( {
							...link,
							rel: link.rel[ 0 ],
						} ) ),
					},
				},
				byId: {
					...state.byId,
					...fromPairs(
						action.items
							.filter( ( { uuid } ) => {
								if ( ! state.byId[ uuid ] ) {
									return true;
								}

								return (
									state.byId[ uuid ].context === 'embed' ||
									state.byId[ uuid ].context ===
										action.context
								);
							} )
							.map( ( email ) => [
								email.uuid,
								{
									context: action.context,
									email,
								},
							] )
					),
				},
			};
		case START_QUERY:
			return {
				...state,
				querying: [ ...state.querying, action.queryId ],
			};
		case FINISH_QUERY:
		case FAILED_QUERY:
			return {
				...state,
				querying: state.querying.filter(
					( queryId ) => queryId !== action.queryId
				),
			};
		case RECEIVE_EMAIL:
			return {
				...state,
				byId: {
					...state.byId,
					[ action.email.uuid ]: {
						context: action.context,
						email: action.email,
					},
				},
			};
		case START_FETCH_EMAIL:
			return {
				...state,
				fetching: [ ...state.fetching, action.id ],
			};
		case FINISH_FETCH_EMAIL:
		case FAILED_FETCH_EMAIL:
			return {
				...state,
				fetching: state.fetching.filter( ( id ) => id !== action.id ),
			};
		case START_SEND_EMAIL:
			return {
				...state,
				sending: [ ...state.sending, action.id ],
			};
		case FINISH_SEND_EMAIL:
		case FAILED_SEND_EMAIL:
			return {
				...state,
				sending: state.sending.filter( ( id ) => id !== action.id ),
			};
		case START_DELETE_EMAIL:
			return {
				...state,
				deleting: [ ...state.deleting, action.id ],
			};
		case FINISH_DELETE_EMAIL:
			return {
				...state,
				deleting: state.deleting.filter( ( id ) => id !== action.id ),
				byId: omit( state.byId, [ action.id ] ),
			};
		case FAILED_DELETE_EMAIL:
			return {
				...state,
				deleting: state.deleting.filter( ( id ) => id !== action.id ),
			};
		case START_EMPTY_INBOX:
			return {
				...state,
				emptyingInbox: true,
			};
		case FINISH_EMPTY_INBOX:
			return {
				...state,
				emptyingInbox: false,
				queries: {},
				byId: {},
			};
		case FAILED_EMPTY_INBOX:
			return {
				...state,
				emptyingInbox: false,
			};
		case RECEIVE_SETTINGS:
			return {
				...state,
				settings: {
					...action.settings,
				},
			};
		default:
			return state;
	}
}
