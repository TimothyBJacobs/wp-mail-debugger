<?php
declare( strict_types=1 );
/**
 * Email uuid Value Object.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Domain\Email;

final class EmailUuid {

	/** @var string */
	private $uuid;

	/**
	 * EmailUuid constructor.
	 *
	 * @param string $uuid
	 */
	public function __construct( string $uuid ) {
		if ( ! wp_is_uuid( $uuid, 4 ) ) {
			throw new InvalidUuid( __( 'Malformed uuid.', 'wp-mail-debugger' ) );
		}

		$this->uuid = $uuid;
	}

	/**
	 * Generate a new uuid.
	 *
	 * @return EmailUuid
	 */
	public static function generate(): self {
		return new self( wp_generate_uuid4() );
	}

	/**
	 * Is this email uuid the same as the given email uuid.
	 *
	 * @param EmailUuid $uuid
	 *
	 * @return bool
	 */
	public function equals( EmailUuid $uuid ): bool {
		return $this->uuid === $uuid->uuid;
	}

	/**
	 * Get the raw uuid value.
	 *
	 * @return string
	 */
	public function get_uuid(): string {
		return $this->uuid;
	}

	public function __toString() {
		return $this->uuid;
	}
}
