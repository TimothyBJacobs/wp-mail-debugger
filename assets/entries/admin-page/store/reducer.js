/**
 * Internal dependencies
 */
import {
	ENABLE_SEARCH,
	DISABLE_SEARCH,
	VIEW_EMAIL,
	VIEW_LIST,
} from './actions';

const DEFAULT_STATE = {
	currentEmail: '',
	searchEnabled: false,
};

export default function reducer( state = DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case VIEW_EMAIL:
			return {
				...state,
				currentEmail: action.id,
			};
		case VIEW_LIST:
			return {
				...state,
				currentEmail: '',
			};
		case ENABLE_SEARCH:
			return {
				...state,
				searchEnabled: true,
			};
		case DISABLE_SEARCH:
			return {
				...state,
				searchEnabled: false,
			};
		default:
			return DEFAULT_STATE;
	}
}
