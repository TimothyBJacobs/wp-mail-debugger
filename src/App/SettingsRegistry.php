<?php
declare( strict_types=1 );
/**
 * Register the app's settings.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2020 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\App;

use TimothyBJacobs\WPMailDebugger\Infrastructure\Runnable;

final class SettingsRegistry implements Runnable {
	public const CAPTURE_MODE = 'wp_mail_debugger_capture_mode';

	public function run(): void {
		add_action( 'init', \Closure::fromCallable( [ $this, 'register_settings' ] ) );
	}

	/**
	 * Register the settings for the mail debugger.
	 */
	private function register_settings(): void {
		register_setting( 'wp-mail-debugger', self::CAPTURE_MODE, [
			'type'              => 'string',
			'default'           => 'filter',
			'show_in_rest'      => [
				'type' => 'string',
				'enum' => [ 'filter', 'override' ],
			],
			'sanitize_callback' => static function ( $value ) {
				if ( ! in_array( $value, [ 'filter', 'override' ], true ) ) {
					$value = 'filter';
				}

				return $value;
			}
		] );
	}

}
