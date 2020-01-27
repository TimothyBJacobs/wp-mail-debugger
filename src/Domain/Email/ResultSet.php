<?php
declare( strict_types=1 );
/**
 * Result set.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2020 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Domain\Email;

use Exception;
use Traversable;

final class ResultSet implements \IteratorAggregate, \Countable {

	/** @var Email[] */
	private $items;

	/** @var int */
	private $total_found;

	/**
	 * ResultSet constructor.
	 *
	 * @param Email[] $items
	 * @param int     $total_found
	 */
	public function __construct( array $items, int $total_found ) {
		$this->items       = $items;
		$this->total_found = $total_found;
	}

	/**
	 * Get the total number of records found for this query.
	 *
	 * @return int
	 */
	public function get_total_found(): int {
		return $this->total_found;
	}

	/**
	 * @return Email[]|Traversable
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->items );
	}

	public function count() {
		return count( $this->items );
	}
}
