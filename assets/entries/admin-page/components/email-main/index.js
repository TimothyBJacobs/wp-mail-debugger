/**
 * Internal dependencies
 */
import EmailRecipients from '../email-recipients';
import SentAt from '../sent-at';
import Message from '../message';
import './style.css';

function EmailMain( { email } ) {
	return (
		<article className="wmd-email-main">
			<header className="wmd-email-main-header">
				<h2 className="wmd-email-main-header__subject">
					{ email.subject }
				</h2>
				<SentAt
					className="wmd-email-main-header__sent-at"
					email={ email }
				/>
				<EmailRecipients
					className="wmd-email-main-header__recipients"
					email={ email }
				/>
			</header>
			<Message email={ email } />
		</article>
	);
}

export default EmailMain;
