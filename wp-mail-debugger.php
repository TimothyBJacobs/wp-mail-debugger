<?php
declare( strict_types=1 );
/*
 * Plugin Name: WP Mail Debugger
 * Plugin URI: https://wpmaildebugger.com
 * Description: Capture and display all email sent through wp_mail().
 * Version: 1.1
 * Text Domain: wp-mail-debugger
 * Author: Timothy Jacobs
 * Author URI: https://timothybjacobs.com
 * Requires PHP: 7.2.0
 * Requires at least: 6.3.0
 * Network: true
 */
namespace TimothyBJacobs\WPMailDebugger;

use Psr\Container\ContainerInterface;
use TimothyBJacobs\WPMailDebugger\App\SettingsRegistry;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( ! class_exists( Plugin::class ) ) {
	add_action( 'admin_notices', static function () {
		if ( current_user_can( 'manage_options' ) ) {
			$msg = esc_html__( 'Composer loader not configured.', 'wp-mail-debugger' );
			echo '<div class="notice notice-error"><p>' . $msg . '</p></div>';
		}
	} );

	return;
}

/**
 * Return the container for the plugin.
 *
 * @return ContainerInterface
 */
function container(): ContainerInterface {
	static $container;

	if ( ! $container ) {
		$container = require __DIR__ . '/container.php';
	}

	return $container;
}

add_action( 'plugins_loaded', static function () {
	container()->get( Plugin::class )->run();
} );

if ( get_option( SettingsRegistry::CAPTURE_MODE ) === 'override' ) {
	if ( function_exists( 'wp_mail' ) ) {
		add_action( 'admin_notices', static function () {
			if ( current_user_can( 'manage_options' ) ) {
				$msg = esc_html__( 'Another plugin is defining wp_mail() before WP Mail Debugger can be initialized. Either disable other mail plugins, or change the Capture Mode to "Filter".', 'wp-mail-debugger' );
				echo '<div class="notice notice-error"><p>' . $msg . '</p></div>';
			}
		} );

		return;
	}

	require_once __DIR__ . '/src/pluggable.php';
}
