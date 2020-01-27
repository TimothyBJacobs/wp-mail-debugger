<?php
declare( strict_types=1 );
/**
 * wp_mail() pluggable definition.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2020 (c) Iron Bound Designs.
 * @license     GPLv2
 */

/**
 * WP Mail definition.
 *
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param string $headers
 * @param array  $attachments
 *
 * @return true
 */
function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	/**
	 * Signals to the WPMailListener to capture the email being sent.
	 *
	 * @param array $args
	 */
	do_action( 'wp_mail_debugger.sent_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );

	return true;
}
