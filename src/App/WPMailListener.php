<?php
declare( strict_types=1 );
/**
 * Listens for wp_mail() function calls, and logs the email.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\App;

use DateTimeImmutable;
use TimothyBJacobs\WPMailDebugger\Domain\Email\Address;
use TimothyBJacobs\WPMailDebugger\Domain\Email\Email;
use TimothyBJacobs\WPMailDebugger\Domain\Email\EmailsRepository;
use TimothyBJacobs\WPMailDebugger\Domain\Email\EmailUuid;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Runnable;

final class WPMailListener implements Runnable {

	/** @var EmailsRepository */
	private $repository;

	/** @var array */
	private $last_args;


	/**
	 * WPMailListener constructor.
	 *
	 * @param EmailsRepository $repository
	 */
	public function __construct( EmailsRepository $repository ) {
		$this->repository = $repository;
	}

	public function run(): void {
		add_filter( 'wp_mail', \Closure::fromCallable( [ $this, 'capture_email' ] ), PHP_INT_MAX );
		add_action( 'wp_mail_succeeded', \Closure::fromCallable( [ $this, 'record_send' ] ) );
		add_action( 'wp_mail_failed', \Closure::fromCallable( [ $this, 'record_fail' ] ) );
		add_action( 'wp_mail_debugger.sent_mail', \Closure::fromCallable( [ $this, 'record_send' ] ) );
	}

	/**
	 * Captures the email args for later processing.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function capture_email( array $args ): array {
		$this->last_args = $args;

		return $args;
	}

	/**
	 * Records when a message is sent.
	 *
	 * @param array $args
	 */
	private function record_send( $args ) {
		try {
			$email = $this->hydrate( $args );
			$this->repository->persist( $email );
		} catch ( \Throwable $e ) {
			// Todo: Setup logging
			error_log( $e->getMessage() );
		}
	}

	/**
	 * Records when a message fails to send.
	 *
	 * @param \WP_Error $error
	 *
	 * @return void
	 */
	private function record_fail( \WP_Error $error ) {
		$data = $error->get_error_data( 'wp_mail_failed' );

		if ( ! $data ) {
			return;
		}

		try {
			$email = $this->hydrate( $data );
			$email->update_meta( 'errors', $error->get_error_messages() );

			if ( ! empty( $data['phpmailer_exception_code'] ) ) {
				$email->update_meta( 'phpmailer_exception_code', $data['phpmailer_exception_code'] );
			}

			$this->repository->persist( $email );
		} catch ( \Throwable $e ) {
			// Todo: Setup logging
			error_log( $e->getMessage() );
		}
	}

	/**
	 * Hydrates an Email object from a wp_mail() message.
	 *
	 * @param array $args
	 *
	 * @return Email
	 * @throws \Exception
	 */
	private function hydrate( array $args ): Email {
		$to          = $args['to'] ?? [];
		$subject     = (string) ( $args['subject'] ?? '' );
		$message     = (string) ( $args['message'] ?? '' );
		$headers     = $args['headers'] ?? [];
		$attachments = $args['attachments'] ?? [];

		if ( ! is_array( $to ) ) {
			$to = explode( ',', $to );
		}

		// The headers passed to the mail actions are modified. Try and get the original headers.
		if ( isset( $this->last_args['message'] ) && $this->last_args['message'] === $message ) {
			$headers = $this->last_args['headers'] ?? $headers;
		}

		if ( ! is_array( $headers ) ) {
			$headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		}

		$email = new Email(
			EmailUuid::generate(),
			new DateTimeImmutable( 'now', new \DateTimeZone( 'UTC' ) ),
			array_map( [ Address::class, 'from_line' ], $to ),
			$subject,
			$message,
			$headers,
			[],
			get_current_blog_id()
		);

		/**
		 * Fires when a wp_mail() send is being recorded.
		 *
		 * @since 1.0.0
		 *
		 * @param Email $email
		 * @param array $args
		 */
		do_action( 'wp_mail_debugger.record_send', $email, compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );

		return $email;
	}
}
