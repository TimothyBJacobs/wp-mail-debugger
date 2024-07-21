/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { formatAddress } from '../../../shared/utils';
import './style.css';

function AddressList( { label, addresses } ) {
	return (
		<>
			<dt>{ label }</dt>
			<dd>{ addresses.map( formatAddress ).join( ', ' ) }</dd>
		</>
	);
}

export default function EmailRecipients( { email, className } ) {
	const to = email.to || [];
	const cc = email.cc || [];
	const bcc = email.bcc || [];

	return (
		<dl className={ classnames( 'wmd-email-recipients', className ) }>
			{ to.length > 0 && (
				<AddressList
					label={ __( 'To:', 'wp-mail-debugger' ) }
					addresses={ to }
				/>
			) }
			{ cc.length > 0 && (
				<AddressList
					label={ __( 'Cc:', 'wp-mail-debugger' ) }
					addresses={ cc }
				/>
			) }
			{ bcc.length > 0 && (
				<AddressList
					label={ __( 'Bcc:', 'wp-mail-debugger' ) }
					addresses={ bcc }
				/>
			) }
		</dl>
	);
}
