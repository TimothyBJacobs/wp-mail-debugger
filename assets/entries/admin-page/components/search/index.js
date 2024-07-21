/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState, useContext } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { CORE_STORE, ADMIN_PAGE_STORE } from '../../../shared/constants';
import Context from '../../context';
import './style.css';

function Search() {
	const [ searchTerm, setSearchTerm ] = useState( '' );
	const isSearching = useSelect( ( select ) =>
		select( CORE_STORE ).isQuerying( 'search' )
	);
	const isSearchEnabled = useSelect( ( select ) =>
		select( ADMIN_PAGE_STORE ).isSearchEnabled()
	);
	const { query } = useDispatch( CORE_STORE );
	const { enableSearch, disableSearch } = useDispatch( ADMIN_PAGE_STORE );
	const { isNetworkAdmin } = useContext( Context );

	const search = ( e ) => {
		e.preventDefault();

		if ( ! isSearchEnabled ) {
			enableSearch();
		}

		query( 'search', {
			context: 'embed',
			search: searchTerm,
			global: isNetworkAdmin,
		} );
	};
	const cancel = () => {
		setSearchTerm( '' );
		disableSearch();
	};

	return (
		<form className="wmd-search" onSubmit={ search }>
			<TextControl
				value={ searchTerm }
				onChange={ setSearchTerm }
				className="wmd-search__control"
				placeholder={ __( 'Search', 'LION' ) }
			/>
			<Button
				icon="search"
				type="submit"
				isBusy={ isSearching }
				className="wmd-search__trigger"
				disabled={ searchTerm.length === 0 }
			/>
			<Button
				icon="no-alt"
				className="wmd-search__trigger"
				onClick={ cancel }
				disabled={ ! isSearchEnabled }
			/>
		</form>
	);
}

export default Search;
