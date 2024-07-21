/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Notice } from '@wordpress/components';

/**
 * Internal dependencies
 */
import SidebarItem from '../../sidebar-item';
import './style.css';

export default function Errors( { email } ) {
	if ( ! email.meta.errors?.length ) {
		return null;
	}

	return (
		<SidebarItem title={ __( 'Errors', 'wp-mail-debugger' ) } slug="errors">
			{ email.meta.errors.map( ( message, i ) => (
				<Notice key={ i } status="error" isDismissible={ false }>
					{ message }
				</Notice>
			) ) }
		</SidebarItem>
	);
}
