<?php
declare( strict_types=1 );
/**
 * Asset manager interface.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure\AssetManager;

interface AssetManager {

	/**
	 * Enqueue the given asset.
	 *
	 * @param string $asset
	 */
	public function enqueue( string $asset ): void;
}
