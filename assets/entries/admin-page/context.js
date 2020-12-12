/**
 * WordPress dependencies
 */
import { createContext } from '@wordpress/element';

const Context = createContext( {
	isNetworkAdmin: false,
} );

export default Context;
