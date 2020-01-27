<?php
declare( strict_types=1 );
/**
 * Container.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger;

use Pimple\Container;

return new \Pimple\Psr11\Container( new Container( require __DIR__ . '/config.php' ) );
