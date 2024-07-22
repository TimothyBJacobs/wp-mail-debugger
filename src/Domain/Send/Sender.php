<?php

namespace TimothyBJacobs\WPMailDebugger\Domain\Send;

use TimothyBJacobs\WPMailDebugger\Domain\Email\Address;
use TimothyBJacobs\WPMailDebugger\Domain\Email\Email;

interface Sender {
	/**
	 * Sends the provided email with wp_mail() again.
	 *
	 * @param Email     $email The email to send.
	 * @param Address[] $to    If specified, a list of new recipients.
	 *
	 * @return void
	 */
	public function send( Email $email, array $to );
}
