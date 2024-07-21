/**
 * External dependencies
 */
import { map, isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';

/**
 * Internal dependencies
 */
import SidebarItem from '../../sidebar-item';
import './style.css';

export default function Headers( { email } ) {
	if ( isEmpty( email.headers ) ) {
		return null;
	}

	return (
		<SidebarItem
			title={ __( 'Headers', 'wp-mail-debugger' ) }
			slug="headers"
		>
			<dl>
				{ map( email.headers, ( value, header ) => (
					<Fragment key={ header }>
						<dt>{ `${ header }:` }</dt>
						<dd>{ value }</dd>
					</Fragment>
				) ) }
			</dl>
		</SidebarItem>
	);
}
