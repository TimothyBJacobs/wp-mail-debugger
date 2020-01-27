<?php
declare( strict_types=1 );
/**
 * Address value object.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Domain\Email;

final class Address {

	/** @var string */
	private $email;

	/** @var string */
	private $name;

	/**
	 * Address constructor.
	 *
	 * @param string $email
	 * @param string $name
	 */
	public function __construct( string $email, string $name = '' ) {
		$this->email = $email;
		$this->name  = $name;
	}

	/**
	 * Construct an address from a header line.
	 *
	 * @param string $line
	 *
	 * @return Address
	 */
	public static function from_line( string $line ): self {
		if ( preg_match( '/(.*)<(.+)>/', $line, $matches ) && count( $matches ) === 3 ) {
			return new self( $matches[2], $matches[1] );
		}

		return new self( $line );
	}

	/**
	 * Get the email address.
	 *
	 * @return string
	 */
	public function get_email(): string {
		return $this->email;
	}

	/**
	 * Get the name provided for this address.
	 *
	 * @return string Name, or empty string if none set.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Convert the email address to a string.
	 *
	 * @return string
	 */
	public function __toString() {
		if ( $name = $this->get_name() ) {
			return sprintf( '%s <%s>', $name, $this->get_email() );
		}

		return $this->get_email();
	}
}
