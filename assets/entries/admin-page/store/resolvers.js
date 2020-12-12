/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import { CORE_STORE } from '../../shared/constants';

export function *getEmails( isNetworkAdmin ) {
	yield dispatch( CORE_STORE, 'query', 'main', { context: 'embed', global: isNetworkAdmin } );
}
