<?php

namespace TimothyBJacobs\WPMailDebugger\Infrastructure\Send;

use TimothyBJacobs\WPMailDebugger\Domain\Email\Email;
use TimothyBJacobs\WPMailDebugger\Domain\Send\Sender;

final class WPMailSender implements Sender {
	public function send( Email $email, array $to ) {
		if ( ! $to ) {
			$to = $email->get_to();
		}

		$to      = array_map( 'strval', $to );
		$subject = $email->get_subject();
		$message = $email->get_message();
		$headers = $email->get_raw_headers();

		if ( is_multisite() ) {
			switch_to_blog( $email->get_site_id() );
		}

		wp_mail( $to, $subject, $message, $headers );

		if ( is_multisite() ) {
			restore_current_blog();
		}
	}
}
