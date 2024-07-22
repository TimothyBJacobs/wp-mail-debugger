/**
 * WordPress dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Button, Popover, TextControl } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { CORE_STORE } from '../../../shared/constants';
import './style.css';

export default function ResendAction( { email } ) {
	const id = useInstanceId( ResendAction, 'wmd-resend-action' );
	const [ open, setOpen ] = useState( false );
	const [ toEmail, setToEmail ] = useState( '' );
	const { settings, isSending } = useSelect(
		( select ) => ( {
			settings: select( CORE_STORE ).getSettings(),
			isSending: select( CORE_STORE ).isSending( email.uuid ),
		} ),
		[ email.uuid ]
	);
	const { sendEmail } = useDispatch( CORE_STORE );
	const send = ( e ) => {
		e.preventDefault();
		setOpen( false );
		sendEmail( email.uuid, toEmail?.length > 0 ? [ toEmail ] : [] );
	};

	return (
		<>
			<Button
				icon="email"
				className="wmd-detail-actions--send"
				disabled={ settings.capture_mode === 'override' || isSending }
				isBusy={ isSending }
				aria-expanded={ open }
				aria-controls={ id }
				onClick={ () => setOpen( ! open ) }
			>
				{ __( 'Resend', 'wp-mail-debugger' ) }
			</Button>
			{ open && (
				<Popover
					id={ id }
					placement="bottom-start"
					headerTitle={ __( 'Resend Email', 'wp-mail-debugger' ) }
					expandOnMobile
					onClose={ () => setOpen( false ) }
				>
					<form className="wmd-resend" onSubmit={ send }>
						<TextControl
							value={ toEmail }
							onChange={ ( next ) => setToEmail( next.trim() ) }
							label={ __( 'Recipient', 'wp-mail-debugger' ) }
							help={ __(
								'Optionally, enter a new email address to send this message to.',
								'wp-mail-debugger'
							) }
						/>
						<Button
							variant="primary"
							text={ __( 'Resend', 'wp-mail-debugger' ) }
							type="submit"
						/>
					</form>
				</Popover>
			) }
		</>
	);
}
