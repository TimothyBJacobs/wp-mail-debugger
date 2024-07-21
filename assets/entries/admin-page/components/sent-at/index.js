/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import './style.css';

export default function SentAt( { email, className } ) {
	return (
		<time
			dateTime={ email.sent_at }
			className={ classnames( 'wmd-sent-at', className ) }
		>
			{ dateI18n( 'M j g:i a', email.sent_at ) }
		</time>
	);
}
