/**
 * Internal dependencies
 */
import Headers from '../sidebar-items/headers';
import { SidebarSlot } from '../../api/sidebar';
import './style.css';

function EmailSidebar( { email } ) {
	return (
		<div className="wmd-email-sidebar">
			<Headers email={ email } />
			<SidebarSlot fillProps={ { email } } />
		</div>
	);
}

export default EmailSidebar;
