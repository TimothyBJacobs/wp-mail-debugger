<?php
declare( strict_types=1 );
/**
 * Installable interface.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure;

interface Installable {

	/**
	 * Run code on plugin installation.
	 */
	public function install(): void;
}
