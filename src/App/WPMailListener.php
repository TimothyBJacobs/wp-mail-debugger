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

	/**
	 * WPMailListener constructor.
	 *
	 * @param EmailsRepository $repository
	 */
	public function __construct( EmailsRepository $repository ) {
		$this->repository = $repository;
	}

	public function run(): void {
		add_filter( 'wp_mail', \Closure::fromCallable( [ $this, 'record_send' ] ), PHP_INT_MAX );
		add_action( 'wp_mail_debugger.sent_mail', \Closure::fromCallable( [ $this, 'record_send' ] ) );
	}

	/**
	 * Listens to the 'wp_mail' filter.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function record_send( $args ) {
		$to      = $args['to'] ?? [];
		$headers = $args['headers'] ?? [];

		if ( ! is_array( $to ) ) {
			$to = explode( ',', $to );
		}

		if ( ! is_array( $headers ) ) {
			$headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		}

		try {
			$email = new Email(
				EmailUuid::generate(),
				new DateTimeImmutable( 'now', new \DateTimeZone( 'UTC' ) ),
				array_map( [ Address::class, 'from_line' ], $to ),
				(string) ( $args['subject'] ?? '' ),
				(string) ( $args['message'] ?? '' ),
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
			do_action( 'wp_mail_debugger.record_send', $email, $args );
			$this->repository->persist( $email );
		} catch ( \Throwable $e ) {
			// Todo: Setup logging
			error_log( $e->getMessage() );
		}

		return $args;
	}
}
