<?php
declare( strict_types=1 );
/**
 * Version Manager that stores version information in wp_options.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure\VersionManager;

use Psr\Container\ContainerInterface;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Installable;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Upgradable;

final class WPOptionVersionManager implements VersionManager {

	private const OPTION = 'tbj-wp-mail-debugger';

	/** @var ContainerInterface */
	private $container;

	/** @var string[] */
	private $installable;

	/** @var string[] */
	private $upgradable;

	/**
	 * WPOptionVersionManager constructor.
	 *
	 * @param ContainerInterface $container
	 * @param string[]           $installable
	 * @param string[]           $upgradable
	 */
	public function __construct( ContainerInterface $container, array $installable, array $upgradable ) {
		$this->container   = $container;
		$this->installable = $installable;
		$this->upgradable  = $upgradable;
	}

	public function run( int $to_build ): void {
		$known_build = $this->get_known_build();

		if ( $known_build === $to_build ) {
			return;
		}

		if ( 0 === $known_build ) {
			foreach ( $this->installable as $installable ) {
				/** @var Installable $installable */
				$installable = $this->container->get( $installable );
				$installable->install();
			}

			$known_build ++;
		}

		for ( $build = $known_build; $build <= $to_build; $build ++ ) {
			foreach ( $this->upgradable as $upgradable ) {
				/** @var Upgradable $upgradable */
				$upgradable = $this->container->get( $upgradable );
				$upgradable->do_upgrade( $build );
			}
		}

		update_site_option( self::OPTION, $to_build );
	}

	private function get_known_build(): int {
		return (int) get_site_option( self::OPTION, 0 );
	}
}
