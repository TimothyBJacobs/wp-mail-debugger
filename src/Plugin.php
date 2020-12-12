<?php
declare( strict_types=1 );
/**
 * Main plugin class.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger;

use TimothyBJacobs\WPMailDebugger\Infrastructure\Runner\Runner;
use TimothyBJacobs\WPMailDebugger\Infrastructure\VersionManager\VersionManager;

final class Plugin {

	public const BUILD = 3;

	/** @var VersionManager */
	private $version_manager;
	/** @var Runner */
	private $runner;

	/**
	 * Plugin constructor.
	 *
	 * @param Runner         $runner
	 * @param VersionManager $version_manager
	 */
	public function __construct( Runner $runner, VersionManager $version_manager ) {
		$this->runner          = $runner;
		$this->version_manager = $version_manager;
	}

	/**
	 * Run the plugin.
	 */
	public function run(): void {
		$this->version_manager->run( self::BUILD );
		$this->runner->run();
	}
}
