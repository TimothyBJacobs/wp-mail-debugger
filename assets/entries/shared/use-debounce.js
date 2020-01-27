/**
 * External dependencies
 */
import { debounce } from 'lodash';

/**
 * WordPress dependencies
 */
import { useCallback, useRef, useState, useEffect } from '@wordpress/element';

/**
 * Use a debounced callback.
 *
 * @author gnbaron (use-lodash-debounce) MIT
 *
 * @param {Function} callback Callback function to apply.
 * @param {number} delay Delay in ms.
 * @param {Object} options Lodash options.
 * @return {Function} Debounced function.
 */
export function useDebouncedCallback( callback, delay = 0, options ) {
	return useCallback( debounce( callback, delay, options ), [
		callback,
		delay,
		options,
	] );
}

/**
 * Use a debounced value.
 *
 * @param {*} value Value
 * @param {number} delay Delay in ms.
 * @param {Object} options Lodash options.
 * @return {*} Debounced value.
 */
export function useDebounce( value, delay = 0, options ) {
	const previousValue = useRef( value );
	const [ current, setCurrent ] = useState( value );
	const debouncedCallback = useDebouncedCallback( ( debouncedValue ) => setCurrent( debouncedValue ), delay, options );
	useEffect( () => {
		// does trigger the debounce timer initially
		if ( value !== previousValue.current ) {
			debouncedCallback( value );
			previousValue.current = value;
			// cancel the debounced callback on clean up
			return debouncedCallback.cancel;
		}
	}, [ value ] );
	return current;
}
