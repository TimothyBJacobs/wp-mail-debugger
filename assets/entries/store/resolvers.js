import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
import { apiFetch } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import { fetchEmail, receiveSettings } from './actions';
import { getEmail as getEmailSelector } from './selectors';
import { filterSettings } from '../shared/utils';

export const getEmail = {
	*fulfill( id, context = 'view' ) {
		yield* fetchEmail( id, context );
	},
	isFulfilled( state, id, context = 'view' ) {
		return !! getEmailSelector( state, id, context );
	},
};

export const getSettings = {
	*fulfill() {
		const settings = yield apiFetch( {
			path: 'wp/v2/settings',
		} );
		const filtered = filterSettings( settings );

		yield receiveSettings( filtered );
	},
	isFulfilled( state ) {
		return ! isEmpty( state.settings );
	},
};
