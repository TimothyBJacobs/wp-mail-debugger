/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import { CORE_STORE } from '../../shared/constants';

export function *getEmails() {
	yield dispatch( CORE_STORE, 'query', 'main', { context: 'embed' } );
}
