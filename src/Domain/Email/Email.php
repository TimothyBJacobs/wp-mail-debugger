<?php
declare( strict_types=1 );
/**
 * Email entity.
 *
 * @since       1.0
 * @author      Iron Bound Designs
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Domain\Email;

use DateTimeInterface;

final class Email {

	/** @var EmailUuid */
	private $uuid;

	/** @var DateTimeInterface */
	private $sent_at;

	/** @var Address */
	private $from;

	/** @var Address[] */
	private $to = [];

	/** @var Address[] */
	private $cc = [];

	/** @var Address[] */
	private $bcc = [];

	/** @var string */
	private $subject;

	/** @var string */
	private $message;

	/** @var string[] */
	private $headers;

	/** @var string[] */
	private $raw_headers;

	/** @var array */
	private $meta;

	/** @var int */
	private $site_id;

	/**
	 * Email constructor.
	 *
	 * @param EmailUuid         $uuid
	 * @param DateTimeInterface $sent_at
	 * @param Address[]         $to
	 * @param string            $subject
	 * @param string            $message
	 * @param string[]          $headers
	 * @param array             $meta
	 * @param int               $site_id
	 */
	public function __construct(
		EmailUuid $uuid,
		DateTimeInterface $sent_at,
		iterable $to,
		string $subject,
		string $message,
		array $headers,
		array $meta = [],
		int $site_id = 0
	) {
		$this->uuid        = $uuid;
		$this->sent_at     = $sent_at;
		$this->to          = $to;
		$this->subject     = $subject;
		$this->message     = $message;
		$this->raw_headers = $headers;
		$this->headers     = $this->parse_headers( $headers );
		$this->meta        = $meta;
		$this->site_id     = $site_id;
	}

	/**
	 * @return EmailUuid
	 */
	public function get_uuid(): EmailUuid {
		return $this->uuid;
	}

	/**
	 * Get the time this email was sent.
	 *
	 * @return DateTimeInterface
	 */
	public function get_sent_at(): DateTimeInterface {
		return $this->sent_at;
	}

	/**
	 * @return Address
	 */
	public function get_from(): Address {
		return $this->from;
	}

	/**
	 * @return Address[]
	 */
	public function get_to(): array {
		return $this->to;
	}

	/**
	 * @return Address[]
	 */
	public function get_cc(): array {
		return $this->cc;
	}

	/**
	 * @return Address[]
	 */
	public function get_bcc(): array {
		return $this->bcc;
	}

	/**
	 * @return string
	 */
	public function get_subject(): string {
		return $this->subject;
	}

	/**
	 * @return string
	 */
	public function get_message(): string {
		return $this->message;
	}

	/**
	 * @return array
	 */
	public function get_headers(): array {
		return $this->headers;
	}

	/**
	 * @return string[]
	 */
	public function get_raw_headers(): array {
		return $this->raw_headers;
	}

	/**
	 * Gets the site ID the email was sent from.
	 *
	 * @return int
	 */
	public function get_site_id(): int {
		return $this->site_id;
	}

	/**
	 * Get all the meta associated with this email.
	 *
	 * @return array
	 */
	public function get_all_meta(): array {
		return $this->meta;
	}

	/**
	 * Get a meta value.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_meta( string $key, $default = null ) {
		return $this->meta[ $key ] ?? $default;
	}

	/**
	 * Update a meta value.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function update_meta( string $key, $value ): void {
		$this->meta[ $key ] = $value;
	}

	/**
	 * Delete a meta value.
	 *
	 * @param string $key
	 */
	public function delete_meta( string $key ): void {
		unset( $this->meta[ $key ] );
	}

	/**
	 * Parse headers into a key => value list, and extract out addresses.
	 *
	 * @param array $headers
	 *
	 * @return array
	 */
	private function parse_headers( array $headers ): array {
		$from_name  = 'wordpress';
		$from_email = '';
		$clean      = [];
		$cc         = $bcc = [];

		foreach ( $headers as $header ) {
			if ( strpos( $header, ':' ) === false ) {
				if ( false !== stripos( $header, 'boundary=' ) ) {
					$parts    = preg_split( '/boundary=/i', trim( $header ) );
					$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
				}

				continue;
			}

			// Explode them out
			[ $name, $content ] = explode( ':', trim( $header ), 2 );

			// Cleanup crew
			$name    = trim( $name );
			$content = trim( $content );

			switch ( strtolower( $name ) ) {
				// Mainly for legacy -- process a From: header if it's there
				case 'from':
					$bracket_pos = strpos( $content, '<' );
					if ( $bracket_pos !== false ) {
						// Text before the bracketed email is the "From" name.
						if ( $bracket_pos > 0 ) {
							$from_name = substr( $content, 0, $bracket_pos - 1 );
							$from_name = str_replace( '"', '', $from_name );
							$from_name = trim( $from_name );
						}

						$from_email = substr( $content, $bracket_pos + 1 );
						$from_email = str_replace( '>', '', $from_email );
						$from_email = trim( $from_email );

						// Avoid setting an empty $from_email.
					} elseif ( '' !== trim( $content ) ) {
						$from_email = trim( $content );
					}
					break;
				case 'cc':
					$cc = array_merge( $cc, explode( ',', $content ) );
					break;
				case 'bcc':
					$bcc = array_merge( $bcc, explode( ',', $content ) );
					break;
			}

			$clean[ trim( $name ) ] = trim( $content );
		}

		if ( ! $from_email ) {
			// Get the site domain and get rid of www.
			$sitename = strtolower( $_SERVER['SERVER_NAME'] ?? '' );
			if ( substr( $sitename, 0, 4 ) == 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}

			$from_email = 'wordpress@' . $sitename;
		}

		$from_email = (string) apply_filters( 'wp_mail_from', $from_email );
		$from_name  = (string) apply_filters( 'wp_mail_from_name', $from_name );

		$this->from = new Address( $from_email, $from_name );

		foreach ( $cc as $line ) {
			$this->cc[] = Address::from_line( $line );
		}

		foreach ( $bcc as $line ) {
			$this->bcc[] = Address::from_line( $line );
		}

		return $clean;
	}
}
