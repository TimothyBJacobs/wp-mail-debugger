/**
 * Parses the fetch response.
 *
 * @param {Response} response The response object from apiFetch.
 * @return {{response: *, type: string}} Data control.
 */
export function parseFetchResponse( response ) {
	return {
		type: 'PARSE_FETCH_RESPONSE',
		response,
	};
}

/**
 * Parse the fetch response into an object with data and headers.
 *
 * @param {Response} response The response object from apiFetch.
 * @return {Promise<*>} Parsed response object.
 */
async function PARSE_FETCH_RESPONSE( { response } ) {
	return await response.json();
}

export default {
	PARSE_FETCH_RESPONSE,
};
