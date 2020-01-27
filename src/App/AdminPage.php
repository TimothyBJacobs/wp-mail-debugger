<?php
declare( strict_types=1 );
/**
 * Admin Page controller.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\App;

use TimothyBJacobs\WPMailDebugger\Infrastructure\AssetManager\AssetManager;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Runnable;

final class AdminPage implements Runnable {

	/** @var AssetManager */
	private $asset_manager;

	/**
	 * AdminPage constructor.
	 *
	 * @param AssetManager $asset_manager
	 */
	public function __construct( AssetManager $asset_manager ) {
		$this->asset_manager = $asset_manager;
	}

	public function run(): void {
		if ( is_multisite() ) {
			add_action( 'network_admin_menu', \Closure::fromCallable( [ $this, 'register_menu' ] ) );
		} else {
			add_action( 'admin_menu', \Closure::fromCallable( [ $this, 'register_menu' ] ) );
		}

		add_action( 'admin_enqueue_scripts', \Closure::fromCallable( [ $this, 'enqueue' ] ) );
	}

	private function register_menu(): void {
		add_management_page(
			__( 'WP Mail Debugger' ),
			__( 'WP Mail Debugger' ),
			'manage_options',
			'wp-mail-debugger',
			\Closure::fromCallable( [ $this, 'render' ] )
		);
	}

	private function enqueue(): void {
		if ( get_current_screen()->id === 'tools_page_wp-mail-debugger' ) {
			$this->asset_manager->enqueue( 'admin-page' );
		}
	}

	private function render(): void {
		echo '<div id="wp-mail-debugger-root"></div>';
	}
}
