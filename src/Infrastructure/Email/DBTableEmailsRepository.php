<?php
declare( strict_types=1 );
/**
 * Emails repository that persists entities to a custom table managed by wpdb.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Infrastructure\Email;

use DateTimeImmutable;
use DateTimeZone;
use TimothyBJacobs\WPMailDebugger\Domain\Email\Address;
use TimothyBJacobs\WPMailDebugger\Domain\Email\Email;
use TimothyBJacobs\WPMailDebugger\Domain\Email\EmailNotFound;
use TimothyBJacobs\WPMailDebugger\Domain\Email\EmailsRepository;
use TimothyBJacobs\WPMailDebugger\Domain\Email\EmailUuid;
use TimothyBJacobs\WPMailDebugger\Domain\Email\ResultSet;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Installable;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Upgradable;

final class DBTableEmailsRepository implements EmailsRepository, Installable, Upgradable {

	private const TN = 'wp_mail_debug_emails';

	/** @var \wpdb */
	private $wpdb;

	/**
	 * wpdbEmailsRepository constructor.
	 *
	 * @param \wpdb $wpdb
	 */
	public function __construct( \wpdb $wpdb ) {
		$this->wpdb                  = $wpdb;
		$this->wpdb->global_tables[] = self::TN;
	}

	public function persist( Email $email ): void {
		$this->wpdb->replace(
			$this->wpdb->base_prefix . self::TN,
			[
				'uuid'       => (string) $email->get_uuid(),
				'to_address' => implode( ',', array_map( 'strval', $email->get_to() ) ),
				'subject'    => $email->get_subject(),
				'message'    => $email->get_message(),
				'headers'    => wp_json_encode( $email->get_raw_headers() ),
				'sent_at'    => $email->get_sent_at()->format( 'Y-m-d H:i:s' ),
				// This is not a good way to handle meta, we should actually setup a separate EAV table. But for now...
				'meta'       => wp_json_encode( $email->get_all_meta() ),
			]
		);


	}

	public function find( EmailUuid $uuid ): Email {
		$tn  = $this->wpdb->base_prefix . self::TN;
		$row = $this->wpdb->get_row( $this->wpdb->prepare(
			"SELECT * FROM {$tn} WHERE `uuid` = %s LIMIT 1",
			(string) $uuid
		), ARRAY_A );

		if ( ! $row ) {
			throw new EmailNotFound( sprintf( __( 'Could not find email identified by \'%s\'', 'wp-mail-debugger' ), (string) $uuid ) );
		}

		return $this->hydrate( $row );
	}

	public function list( string $search = '', int $per_page = 100, int $page = 1 ): ResultSet {
		if ( $page < 1 ) {
			return new ResultSet( [], 0 );
		}

		$offset = ( $page - 1 ) * $per_page;
		$tn     = $this->wpdb->base_prefix . self::TN;

		if ( $search ) {
			$search      = '%' . $this->wpdb->esc_like( $search ) . '%';
			$query       = $this->wpdb->prepare(
				"SELECT * FROM {$tn} WHERE `subject` LIKE %s OR `message` LIKE %s ORDER BY `sent_at` DESC LIMIT {$offset}, {$per_page}",
				$search,
				$search
			);
			$count_query = $this->wpdb->prepare(
				"SELECT count(`uuid`) FROM {$tn} WHERE `subject` LIKE %s OR `message` LIKE %s",
				$search,
				$search
			);
		} else {
			$query       = "SELECT * FROM {$tn} ORDER BY `sent_at` DESC LIMIT {$offset}, {$per_page}";
			$count_query = "SELECT count(`uuid`) FROM {$tn}";
		}

		$rows  = $this->wpdb->get_results( $query, ARRAY_A );
		$total = (int) $this->wpdb->get_var( $count_query );

		$items = array_map( \Closure::fromCallable( [ $this, 'hydrate' ] ), $rows );

		return new ResultSet( $items, $total );
	}

	public function delete( EmailUuid $uuid ): void {
		$this->wpdb->delete(
			$this->wpdb->base_prefix . self::TN,
			[
				'uuid' => (string) $uuid,
			]
		);
	}

	public function delete_all(): void {
		$tn = $this->wpdb->base_prefix . self::TN;
		$this->wpdb->query( "TRUNCATE TABLE {$tn}" );
	}

	private function hydrate( array $data ): Email {
		return new Email(
			new EmailUuid( $data['uuid'] ),
			new DateTimeImmutable( $data['sent_at'], new DateTimeZone( 'UTC' ) ),
			array_map( [ Address::class, 'from_line' ], explode( ',', $data['to_address'] ) ),
			$data['subject'],
			$data['message'],
			json_decode( $data['headers'], true ),
			$data['meta'] ? (array) json_decode( $data['meta'], true ) : []
		);
	}

	public function install(): void {
		$tn = $this->wpdb->base_prefix . self::TN;

		$this->wpdb->query( <<<SQL
CREATE TABLE {$tn} (
    uuid CHAR(36) NOT NULL,
    to_address TEXT NOT NULL,
    subject TEXT NOT NULL,
    message TEXT NOT NULL,
    headers TEXT NOT NULL,
    attachments TEXT,
    sent_at DATETIME,
    PRIMARY KEY (uuid)
) {$this->wpdb->get_charset_collate()}
SQL
		);
	}

	public function do_upgrade( int $build ): void {
		switch ( $build ) {
			case 2:
				$tn = $this->wpdb->base_prefix . self::TN;
				$this->wpdb->query( "ALTER TABLE {$tn} ADD COLUMN meta TEXT" );
				break;
		}
	}

}
