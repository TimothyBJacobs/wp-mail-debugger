/**
 * WordPress dependencies
 */
import { controls } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { CORE_STORE } from '../../shared/constants';

export function* getEmails( isNetworkAdmin ) {
	yield controls.dispatch( CORE_STORE, 'query', 'main', {
		context: 'embed',
		global: isNetworkAdmin,
	} );
}
