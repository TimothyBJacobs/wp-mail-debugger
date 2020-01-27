<?php
declare( strict_types=1 );
/**
 * Thrown when a mal-formed UUID is provided.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Domain\Email;

use TimothyBJacobs\WPMailDebugger\Infrastructure\Exception\Exception;

class InvalidUuid extends \InvalidArgumentException implements Exception {

}
