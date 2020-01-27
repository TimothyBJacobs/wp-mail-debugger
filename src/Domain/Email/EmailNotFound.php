<?php
declare( strict_types=1 );
/**
 * Thrown when an Email cannot be found by its uuid.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Domain\Email;

use TimothyBJacobs\WPMailDebugger\Infrastructure\Exception\EntityNotFound;

class EmailNotFound extends \RuntimeException implements EntityNotFound {

}
