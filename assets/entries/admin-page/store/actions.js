export function viewEmail( id ) {
	return {
		type: VIEW_EMAIL,
		id,
	};
}

export function viewList() {
	return {
		type: VIEW_LIST,
	};
}

export function enableSearch() {
	return {
		type: ENABLE_SEARCH,
	};
}

export function disableSearch() {
	return {
		type: DISABLE_SEARCH,
	};
}

export const VIEW_EMAIL = 'VIEW_EMAIL';
export const VIEW_LIST = 'VIEW_LIST';
export const ENABLE_SEARCH = 'ENABLE_SEARCH';
export const DISABLE_SEARCH = 'DISABLE_SEARCH';
