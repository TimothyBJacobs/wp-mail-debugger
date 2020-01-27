/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
import { controls as dataControls } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import reducer from './store/reducer';
import controls from './shared/controls';
import * as actions from './store/actions';
import * as selectors from './store/selectors';
import * as resolvers from './store/resolvers';
import { CORE_STORE } from './shared/constants';

registerStore( CORE_STORE, {
	controls: {
		...dataControls,
		...controls,
	},
	actions,
	selectors,
	resolvers,
	reducer,
} );
