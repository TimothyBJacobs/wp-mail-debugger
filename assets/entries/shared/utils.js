import { SETTING_PREFIX } from './constants';

/**
 * Format an address.
 *
 * @param {Object} address
 * @param {string} address.email
 * @param {string} address.name
 *
 * @return {string} The formatted address.
 */
export function formatAddress( address ) {
	if ( address.name ) {
		return address.name + ' <' + address.email + '>';
	}

	return address.email;
}

/**
 * Filter the list of settings to only our settings. Stripping the prefix.
 *
 * @param {Object} settings The raw settings.
 * @return {Object} The filtered settings.
 */
export function filterSettings( settings ) {
	const filtered = {};

	for ( const setting in settings ) {
		if ( ! settings.hasOwnProperty( setting ) ) {
			continue;
		}

		if ( setting.startsWith( SETTING_PREFIX ) ) {
			const trimmed = setting.substr( SETTING_PREFIX.length );

			filtered[ trimmed ] = settings[ setting ];
		}
	}

	return filtered;
}
