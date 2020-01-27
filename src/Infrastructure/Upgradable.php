<?php
declare( strict_types=1 );
/**
 * Upgradable interface.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure;

interface Upgradable {

	/**
	 * Do the upgrade for the given build number.
	 *
	 * @param int $build
	 */
	public function do_upgrade( int $build ): void;
}
