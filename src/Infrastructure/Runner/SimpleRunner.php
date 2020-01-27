<?php
declare( strict_types=1 );
/**
 * Simple Runner that runs all Runnable classes.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure\Runner;

use Psr\Container\ContainerInterface;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Runnable;

final class SimpleRunner implements Runner {

	/** @var ContainerInterface */
	private $container;

	/** @var string[] */
	private $runnable;

	/**
	 * SimpleRunner constructor.
	 *
	 * @param ContainerInterface $container
	 * @param string[]           $runnable A list of classes implementing the Runnable interface.
	 */
	public function __construct( ContainerInterface $container, array $runnable ) {
		$this->container = $container;
		$this->runnable  = $runnable;
	}

	public function run(): void {
		foreach ( $this->runnable as $class ) {
			if ( $this->container->has( $class ) ) {
				$runnable = $this->container->get( $class );
			} elseif ( class_exists( $class ) ) {
				$runnable = new $class;
			} else {
				throw new \LogicException( sprintf(
					__( '%s does not resolve to a container definition and is not a class.', 'wp-mail-debugger' ),
					$class
				) );
			}

			if ( ! $runnable instanceof Runnable ) {
				throw new \LogicException( sprintf(
					__( '%s does not implement the Runnable interface, but was provided to the Runner.', 'wp-mail-debugger' ),
					$class
				) );
			}

			$runnable->run();
		}
	}
}
