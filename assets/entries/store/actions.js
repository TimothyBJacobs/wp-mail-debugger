/**
 * WordPress dependencies
 */
import { controls } from '@wordpress/data';
import { apiFetch } from '@wordpress/data-controls';
import { addQueryArgs, getQueryArg } from '@wordpress/url';

/**
 * WordPress dependencies
 */
import { CORE_STORE, SETTING_PREFIX } from '../shared/constants';
import { parseFetchResponse } from '../shared/controls';
import { filterSettings } from '../shared/utils';

export function* query( queryId, queryParams ) {
	let response, items;

	yield { type: START_QUERY, queryId, queryParams };

	try {
		response = yield apiFetch( {
			path: addQueryArgs( 'wp-mail-debugger/v1/emails', queryParams ),
			parse: false,
		} );

		items = yield parseFetchResponse( response );
	} catch ( error ) {
		yield { type: FAILED_QUERY, queryId, queryParams, error };

		return error;
	}

	yield receiveQuery(
		queryId,
		queryParams.context || 'view',
		response,
		items,
		'replace'
	);
	yield { type: FINISH_QUERY, queryId, queryParams, response };

	return response;
}

export function* fetchQueryNextPage( queryId, mode = 'append' ) {
	const link = yield controls.select(
		CORE_STORE,
		'getQueryHeaderLink',
		queryId,
		'next'
	);

	if ( ! link ) {
		return [];
	}

	let response, items;

	yield { type: START_QUERY, queryId };

	try {
		response = yield apiFetch( {
			url: link.link,
			parse: false,
		} );
		items = yield parseFetchResponse( response );
	} catch ( error ) {
		yield { type: FAILED_QUERY, queryId, error };

		return error;
	}

	const context = getQueryArg( link.link, 'context' ) || 'view';
	yield receiveQuery( queryId, context, response, items, mode );
	yield { type: FINISH_QUERY, queryId, response };

	return response;
}

export function* fetchEmail( id, context = 'view' ) {
	let email;

	yield { type: START_FETCH_EMAIL, id };

	try {
		email = yield apiFetch( {
			path: `wp-mail-debugger/v1/emails/${ id }?context=${ context }`,
		} );
	} catch ( error ) {
		yield { type: FAILED_FETCH_EMAIL, id, error };

		return error;
	}

	yield receiveEmail( email, context );
	yield { type: FINISH_FETCH_EMAIL, id, email };
}

export function* sendEmail( id, to ) {
	yield { type: START_SEND_EMAIL, id };

	try {
		yield apiFetch( {
			path: `wp-mail-debugger/v1/emails/${ id }/send`,
			method: 'POST',
			data: { to },
		} );
	} catch ( error ) {
		yield { type: FAILED_SEND_EMAIL, id, error };

		return error;
	}

	yield { type: FINISH_SEND_EMAIL, id };
	yield controls.dispatch( CORE_STORE, 'query', 'main', {
		context: 'embed',
	} );
}

export function* deleteEmail( id ) {
	yield { type: START_DELETE_EMAIL, id };

	try {
		yield apiFetch( {
			path: `wp-mail-debugger/v1/emails/${ id }`,
			method: 'DELETE',
		} );
	} catch ( error ) {
		yield { type: FAILED_DELETE_EMAIL, error };

		return error;
	}

	yield { type: FINISH_DELETE_EMAIL, id };
}

export function* emptyInbox() {
	yield { type: START_EMPTY_INBOX };

	try {
		yield apiFetch( {
			path: 'wp-mail-debugger/v1/emails',
			method: 'DELETE',
		} );
	} catch ( error ) {
		yield { type: FAILED_EMPTY_INBOX, error };

		return error;
	}

	yield { type: FINISH_EMPTY_INBOX };
}

export function* updateSetting( setting, value ) {
	const settings = yield apiFetch( {
		path: 'wp/v2/settings',
		method: 'PUT',
		data: {
			[ SETTING_PREFIX + setting ]: value,
		},
	} );

	const filtered = filterSettings( settings );
	yield receiveSettings( filtered );
}

export function receiveQuery( queryId, context, response, items, mode ) {
	return {
		type: RECEIVE_QUERY,
		queryId,
		context,
		response,
		items,
		mode,
	};
}

export function receiveEmail( email, context ) {
	return {
		type: RECEIVE_EMAIL,
		email,
		context,
	};
}

export function receiveSettings( settings ) {
	return {
		type: RECEIVE_SETTINGS,
		settings,
	};
}

export const RECEIVE_QUERY = 'RECEIVE_QUERY';
export const RECEIVE_EMAIL = 'RECEIVE_EMAIL';
export const RECEIVE_SETTINGS = 'RECEIVE_SETTINGS';

export const START_QUERY = 'START_QUERY';
export const FINISH_QUERY = 'FINISH_QUERY';
export const FAILED_QUERY = 'FAILED_QUERY';

export const START_DELETE_EMAIL = 'START_DELETE_EMAIL';
export const FINISH_DELETE_EMAIL = 'FINISH_DELETE_EMAIL';
export const FAILED_DELETE_EMAIL = 'FAILED_DELETE_EMAIL';

export const START_EMPTY_INBOX = 'START_EMPTY_INBOX';
export const FINISH_EMPTY_INBOX = 'FINISH_EMPTY_INBOX';
export const FAILED_EMPTY_INBOX = 'FAILED_EMPTY_INBOX';

export const START_FETCH_EMAIL = 'START_FETCH_EMAIL';
export const FINISH_FETCH_EMAIL = 'FINISH_FETCH_EMAIL';
export const FAILED_FETCH_EMAIL = 'FAILED_FETCH_EMAIL';

export const START_SEND_EMAIL = 'START_SEND_EMAIL';
export const FINISH_SEND_EMAIL = 'FINISH_SEND_EMAIL';
export const FAILED_SEND_EMAIL = 'FAILED_SEND_EMAIL';
