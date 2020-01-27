<?php
declare( strict_types=1 );
/**
 * Run anything needing to be run during a request.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure\Runner;

interface Runner {

	/**
	 * Run everything needing to be run.
	 */
	public function run(): void;
}
