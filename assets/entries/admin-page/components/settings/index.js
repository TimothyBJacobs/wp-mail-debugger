/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { Button, Popover, RadioControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { CORE_STORE } from '../../../shared/constants';
import './style.css';

function Settings() {
	const [ open, setOpen ] = useState();
	const settings = useSelect( ( select ) =>
		select( CORE_STORE ).getSettings()
	);
	const { updateSetting } = useDispatch( CORE_STORE );

	return (
		<>
			<Button
				icon="admin-settings"
				className="wmd-header__actions--settings"
				onClick={ () => setOpen( ! open ) }
			>
				{ __( 'Settings', 'wp-mail-debugger' ) }
			</Button>
			{ open && (
				<Popover
					placement="bottom-start"
					headerTitle={ __( 'Edit Settings', 'wp-mail-debugger' ) }
					expandOnMobile
					onClose={ () => setOpen( false ) }
				>
					<div className="wmd-settings">
						<RadioControl
							label={ __( 'Capture Mode', 'wp-mail-debugger' ) }
							options={ [
								{
									label: __(
										'Filter wp_mail()',
										'wp-mail-debugger'
									),
									value: 'filter',
								},
								{
									label: __(
										'Override wp_mail()',
										'wp-mail-debugger'
									),
									value: 'override',
								},
							] }
							selected={ settings.capture_mode }
							onChange={ ( captureMode ) =>
								updateSetting( 'capture_mode', captureMode )
							}
							help={ __(
								'How should WP Mail Debugger capture emails. By letting the original mail through and adding filters, or completely overriding wp_mail().',
								'wp-mail-debugger'
							) }
						/>
					</div>
				</Popover>
			) }
		</>
	);
}

export default Settings;
