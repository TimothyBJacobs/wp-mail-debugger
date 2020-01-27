<?php
declare( strict_types=1 );
/**
 * Runnable interface.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure;

interface Runnable {

	/**
	 * Run this object.
	 *
	 * Will be called once per-request.
	 */
	public function run(): void;
}
