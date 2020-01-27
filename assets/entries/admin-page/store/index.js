/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
import { controls } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import { ADMIN_PAGE_STORE } from '../../shared/constants';
import * as actions from './actions';
import * as selectors from './selectors';
import * as resolvers from './resolvers';
import reducer from './reducer';

registerStore( ADMIN_PAGE_STORE, {
	actions,
	controls,
	selectors,
	resolvers,
	reducer,
} );
