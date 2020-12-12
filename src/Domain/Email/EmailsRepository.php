<?php
declare( strict_types=1 );
/**
 * Emails Repository.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\Domain\Email;

interface EmailsRepository {

	/**
	 * Persist an email to storage.
	 *
	 * @param Email $email
	 */
	public function persist( Email $email ): void;

	/**
	 * Find an email by its uuid.
	 *
	 * @param EmailUuid $uuid
	 *
	 * @return Email
	 *
	 * @throws EmailNotFound
	 */
	public function find( EmailUuid $uuid ): Email;

	/**
	 * Get all the emails.
	 *
	 * @param string $search   Search term.
	 * @param int    $per_page Number of records to return per-page.
	 * @param int    $page     The page to iterate from.
	 * @param int    $site_id  The site to include emails from. Pass 0 to query all sites.
	 *
	 * @return ResultSet
	 */
	public function list( string $search = '', int $per_page = 100, int $page = 1, int $site_id = 0 ): ResultSet;

	/**
	 * Delete an email.
	 *
	 * @param EmailUuid $uuid
	 */
	public function delete( EmailUuid $uuid ): void;

	/**
	 * Delete all emails.
	 */
	public function delete_all(): void;
}
