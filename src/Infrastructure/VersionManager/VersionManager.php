<?php
declare( strict_types=1 );
/**
 * Version Manager.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure\VersionManager;

interface VersionManager {

	/**
	 * Run installation and upgrade routines until the given build is reached.
	 *
	 * @param int $to_build
	 */
	public function run( int $to_build ): void;
}
