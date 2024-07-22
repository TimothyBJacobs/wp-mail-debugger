<?php
declare( strict_types=1 );
/**
 * Container configuration.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger;

use Pimple\Container;
use Psr\Container\ContainerInterface;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Runner\SimpleRunner;

return [

	/* App */
	Plugin::class => static function ( Container $c ) {
		return new Plugin(
			$c[ Infrastructure\Runner\Runner::class ],
			$c[ Infrastructure\VersionManager\VersionManager::class ]
		);
	},

	App\WPMailListener::class => static function ( Container $c ) {
		return new App\WPMailListener(
			$c[ Domain\Email\EmailsRepository::class ]
		);
	},

	App\AdminPage::class => static function ( Container $c ) {
		return new App\AdminPage(
			$c[ Infrastructure\AssetManager\AssetManager::class ]
		);
	},

	App\REST\EmailsController::class => static function ( Container $c ) {
		return new App\REST\EmailsController(
			$c[ Domain\Email\EmailsRepository::class ],
			$c[ Domain\Send\Sender::class ]
		);
	},

	/* Domain */

	Domain\Email\EmailsRepository::class => static function ( Container $c ) {
		return $c[ Infrastructure\Email\DBTableEmailsRepository::class ];
	},

	Domain\Send\Sender::class => static function ( Container $c ) {
		return $c[ Infrastructure\Send\WPMailSender::class ];
	},

	/* Infrastructure */

	Infrastructure\Email\DBTableEmailsRepository::class => static function ( Container $c ) {
		return new Infrastructure\Email\DBTableEmailsRepository( $GLOBALS['wpdb'] );
	},

	Infrastructure\Send\WPMailSender::class => static function () {
		return new Infrastructure\Send\WPMailSender();
	},

	Infrastructure\VersionManager\VersionManager::class => static function ( Container $c ) {
		return new Infrastructure\VersionManager\WPOptionVersionManager(
			$c[ ContainerInterface::class ],
			$c['installable'],
			$c['upgradable']
		);
	},

	Infrastructure\Runner\Runner::class => static function ( Container $c ) {
		return new SimpleRunner(
			$c[ ContainerInterface::class ],
			$c['runnable']
		);
	},

	Infrastructure\AssetManager\AssetManager::class   => static function ( Container $c ) {
		return $c[ Infrastructure\AssetManager\WPAssetManager::class ];
	},
	Infrastructure\AssetManager\WPAssetManager::class => static function ( Container $c ) {
		return new Infrastructure\AssetManager\WPAssetManager(
			$c['assets-dir'],
			$c['plugin-file'],
			$c['assets']
		);
	},

	'runnable' => [
		App\SettingsRegistry::class,
		App\WPMailListener::class,
		App\AdminPage::class,
		App\REST\EmailsController::class,
		Infrastructure\AssetManager\WPAssetManager::class,
	],

	'installable' => [
		Infrastructure\Email\DBTableEmailsRepository::class,
	],

	'upgradable' => [
		Infrastructure\Email\DBTableEmailsRepository::class,
	],

	'plugin-file' => __DIR__ . '/wp-mail-debugger.php',

	'assets-dir' => __DIR__ . '/assets/',

	'assets' => [
		'entries' => [
			'store'      => [],
			'admin-page' => [
				'needs_entries' => [ 'store' ],
			],
		]
	],

	ContainerInterface::class => static function ( Container $c ) {
		return new \Pimple\Psr11\Container( $c );
	},
];
