<?php
declare( strict_types=1 );
/**
 * Asset manager that uses {@see wp_scripts()}.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure\AssetManager;

use TimothyBJacobs\WPMailDebugger\Infrastructure\Runnable;
use TimothyBJacobs\WPMailDebugger\Plugin;

final class WPAssetManager implements AssetManager, Runnable {

	private const PREFIX = 'wp-mail-debugger-';

	/** @var string */
	private $assets_dir;

	/** @var string */
	private $plugin_file;

	/** @var array[] */
	private $assets;

	/**
	 * WPAssetManager constructor.
	 *
	 * @param string  $assets_dir
	 * @param string  $plugin_file
	 * @param array[] $assets
	 */
	public function __construct( string $assets_dir, string $plugin_file, array $assets ) {
		$this->assets_dir  = $assets_dir;
		$this->plugin_file = $plugin_file;
		$this->assets      = $assets;
	}

	public function run(): void {
		$register = \Closure::fromCallable( [ $this, 'register' ] );

		add_action( 'admin_enqueue_scripts', $register, 0 );
		add_action( 'wp_enqueue_scripts', $register, 0 );
		add_action( 'login_enqueue_scripts', $register, 0 );
	}

	public function enqueue( string $asset ): void {
		wp_enqueue_script( self::PREFIX . $asset );

		if ( wp_style_is( self::PREFIX . $asset, 'registered' ) ) {
			wp_enqueue_style( self::PREFIX . $asset );
		}
	}

	private function register(): void {
		foreach ( $this->assets['entries'] as $entry => $config ) {
			$asset = require( $this->assets_dir . "build/{$entry}.asset.php" );

			$deps = $asset['dependencies'];

			foreach ( $config['needs_entries'] ?? [] as $needed_entry ) {
				$deps[] = self::PREFIX . $needed_entry;
			}

			wp_register_script(
				self::PREFIX . $entry,
				plugins_url( "assets/build/{$entry}.js", $this->plugin_file ),
				$deps,
				$asset['version']
			);

			if ( in_array( 'wp-i18n', $deps, true ) ) {
				wp_set_script_translations( self::PREFIX . $entry, 'wp-mail-debugger' );
			}

			if ( file_exists( $this->assets_dir . "build/style-{$entry}.css" ) ) {
				$style_deps = [];

				// We need to manually enqueue the CSS for the components library.
				if ( in_array( 'wp-components', $deps, true ) ) {
					$style_deps[] = 'wp-components';
				}

				wp_register_style(
					self::PREFIX . $entry,
					plugins_url( "assets/build/style-{$entry}.css", $this->plugin_file ),
					$style_deps,
					$asset['version']
				);
			}
		}
	}
}
