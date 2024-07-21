/**
 * Internal dependencies
 */
import Errors from '../sidebar-items/errors';
import Headers from '../sidebar-items/headers';
import { SidebarSlot } from '../../api/sidebar';
import './style.css';

function EmailSidebar( { email } ) {
	return (
		<div className="wmd-email-sidebar">
			<Errors email={ email } />
			<Headers email={ email } />
			<SidebarSlot fillProps={ { email } } />
		</div>
	);
}

export default EmailSidebar;
