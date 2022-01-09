<?php
declare( strict_types=1 );
/**
 * Admin Page controller.
 *
 * @since       1.0
 * @author      Iron Bound Designs
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
		add_filter( 'plugin_row_meta', \Closure::fromCallable( [ $this, 'add_row_meta' ] ), 10, 2 );

		add_action( 'network_admin_menu', \Closure::fromCallable( [ $this, 'register_network_menu' ] ) );
		add_action( 'admin_menu', \Closure::fromCallable( [ $this, 'register_menu' ] ) );

		add_action( 'admin_enqueue_scripts', \Closure::fromCallable( [ $this, 'enqueue' ] ) );
	}

	private function add_row_meta( $meta, $file ): array {
		if ( 'wp-mail-debugger/wp-mail-debugger.php' === $file ) {
			$meta[] = sprintf(
				'<a href="%s" id="wmd-mac-link">%s</a>
<script type="application/javascript">
if ( navigator.platform !== "MacIntel" ) {
	var wmdMacLink = document.getElementById("wmd-mac-link");
	wmdMacLink.previousSibling.remove();
	wmdMacLink.remove();
}
</script>',
				'https://apps.apple.com/us/app/wp-mail-debugger/id1547093438?mt=12',
				__( 'Get the Mac App', 'wp-mail-debugger' )
			);
		}

		return $meta;
	}

	private function register_menu(): void {
		add_management_page(
			__( 'WP Mail Debugger', 'wp-mail-debugger' ),
			__( 'WP Mail Debugger', 'wp-mail-debugger' ),
			'manage_options',
			'wp-mail-debugger',
			\Closure::fromCallable( [ $this, 'render' ] )
		);
	}

	private function register_network_menu(): void {
		add_submenu_page(
			'settings.php',
			__( 'WP Mail Debugger', 'wp-mail-debugger' ),
			__( 'WP Mail Debugger', 'wp-mail-debugger' ),
			'manage_network_options',
			'wp-mail-debugger',
			\Closure::fromCallable( [ $this, 'render' ] )
		);
	}

	private function enqueue(): void {
		if ( in_array( get_current_screen()->id, [ 'tools_page_wp-mail-debugger', 'settings_page_wp-mail-debugger-network' ], true ) ) {
			$this->asset_manager->enqueue( 'admin-page' );
		}
	}

	private function render(): void {
		echo '<div id="wp-mail-debugger-root" data-is-network-admin="' . ( is_network_admin() ? '1' : '' ) .'"></div>';
	}
}
