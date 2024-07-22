<?php
declare( strict_types=1 );
/**
 * REST Emails Controller.
 *
 * @since       1.0
 * @author      Iron Bound Designs
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

namespace TimothyBJacobs\WPMailDebugger\App\REST;

use TimothyBJacobs\WPMailDebugger\Domain\Email\Address;
use TimothyBJacobs\WPMailDebugger\Domain\Email\Email;
use TimothyBJacobs\WPMailDebugger\Domain\Email\EmailNotFound;
use TimothyBJacobs\WPMailDebugger\Domain\Email\EmailsRepository;
use TimothyBJacobs\WPMailDebugger\Domain\Email\EmailUuid;
use TimothyBJacobs\WPMailDebugger\Domain\Email\InvalidUuid;
use TimothyBJacobs\WPMailDebugger\Domain\Send\Sender;
use TimothyBJacobs\WPMailDebugger\Infrastructure\Runnable;
use WP_Error;
use WP_Http;
use WP_REST_Controller;
use WP_REST_Server;

final class EmailsController extends WP_REST_Controller implements Runnable {

	/** @var EmailsRepository */
	private $repository;

	/** @var Sender */
	private $sender;

	/**
	 * EmailsController constructor.
	 *
	 * @param EmailsRepository $repository
	 */
	public function __construct( EmailsRepository $repository, Sender $sender ) {
		$this->repository = $repository;
		$this->sender     = $sender;
		$this->namespace  = 'wp-mail-debugger/v1';
		$this->rest_base  = 'emails';
	}

	public function run(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		register_rest_route( $this->namespace, $this->rest_base, [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_items' ],
				'permission_callback' => [ $this, 'delete_items_permissions_check' ],
				'args'                => [],
			],
		] );

		register_rest_route( $this->namespace, $this->rest_base . '/(?P<uuid>[\w\-]+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::READABLE ),
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				'args'                => [],
			],
		] );


		register_rest_route( $this->namespace, $this->rest_base . '/(?P<uuid>[\w\-]+)/send', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'send_item' ],
				'permission_callback' => [ $this, 'send_item_permissions_check' ],
				'args'                => [
					'to' => [
						'type'    => 'array',
						'items'   => [
							'type'   => 'string',
							'format' => 'email',
						],
						'default' => [],
					],
				],
			],
		] );
	}

	public function get_items_permissions_check( $request ) {
		if ( is_multisite() && $request['global'] && ! current_user_can( 'manage_network_options' ) ) {
			return new WP_Error(
				'rest_forbidden_param',
				__( 'Sorry, you must be able to manage the network to see all network emails.', 'wp-mail-debugger' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return current_user_can( 'manage_options' );
	}

	public function get_items( $request ) {
		$search   = (string) $request['search'];
		$per_page = (int) $request['per_page'];
		$page     = (int) $request['page'];

		if ( is_multisite() && $request['global'] ) {
			$site_id = 0;
		} else {
			$site_id = get_current_blog_id();
		}

		$responses = [];
		$emails    = $this->repository->list( $search, (int) $request['per_page'], (int) $request['page'], $site_id );

		foreach ( $emails as $email ) {
			$responses[] = $this->prepare_response_for_collection( $this->prepare_item_for_response( $email, $request ) );
		}

		$response = new \WP_REST_Response( $responses );

		$max_pages = ceil( $emails->get_total_found() / (int) $per_page );

		if ( $page > $max_pages && $emails->get_total_found() ) {
			return new WP_Error(
				'rest_post_invalid_page_number',
				__( 'The page number requested is larger than the number of pages available.', 'wp-mail-debugger' ),
				[ 'status' => 400 ]
			);
		}

		$response->header( 'X-WP-Total', $emails->get_total_found() );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$query = $request->get_query_params();
		array_walk( $query, static function ( &$value, $key ) {
			if ( $key === 'global' ) {
				$value = $value ? 'true' : 'false';
			}
		} );

		$base = add_query_arg( urlencode_deep( $query ), rest_url( $this->namespace . '/' . $this->rest_base ) );

		if ( $page > 1 ) {
			$prev_page = min( $page - 1, $max_pages );
			$prev_link = add_query_arg( 'page', $prev_page, $base );

			$response->link_header( 'prev', $prev_link );
		}

		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	public function get_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function get_item( $request ) {
		try {
			$email = $this->repository->find( new EmailUuid( (string) $request['uuid'] ) );
		} catch ( InvalidUuid $e ) {
			return new WP_Error( 'rest_invalid_email_uuid', $e->getMessage(), [ 'status' => WP_Http::BAD_REQUEST ] );
		} catch ( EmailNotFound $e ) {
			return new WP_Error( 'rest_email_not_found', $e->getMessage(), [ 'status' => WP_Http::NOT_FOUND ] );
		}

		return $this->prepare_item_for_response( $email, $request );
	}

	public function delete_items( $request ): \WP_REST_Response {
		$this->repository->delete_all();

		return new \WP_REST_Response( null, WP_Http::NO_CONTENT );
	}

	public function delete_items_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function delete_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function delete_item( $request ) {
		try {
			$this->repository->delete( new EmailUuid( (string) $request['uuid'] ) );
		} catch ( InvalidUuid $e ) {
			return new WP_Error( 'rest_invalid_email_uuid', $e->getMessage(), [ 'status' => WP_Http::BAD_REQUEST ] );
		}

		return new \WP_REST_Response( null, WP_Http::NO_CONTENT );
	}

	public function send_item_permissions_check( \WP_REST_Request $request ): bool {
		return current_user_can( 'manage_options' );
	}

	public function send_item( \WP_REST_Request $request ) {
		try {
			$email = $this->repository->find( new EmailUuid( (string) $request['uuid'] ) );
		} catch ( InvalidUuid $e ) {
			return new WP_Error( 'rest_invalid_email_uuid', $e->getMessage(), [ 'status' => WP_Http::NOT_FOUND ] );
		}

		$this->sender->send( $email, $request['to'] );

		return new \WP_REST_Response( null, WP_Http::NO_CONTENT );
	}

	public function prepare_item_for_response( $item, $request ) {
		if ( ! $item instanceof Email ) {
			return new \WP_REST_Response( null, WP_Http::INTERNAL_SERVER_ERROR );
		}

		$data = [
			'uuid'    => (string) $item->get_uuid(),
			'site_id' => $item->get_site_id(),
			'sent_at' => $item->get_sent_at()->format( \DateTimeInterface::ATOM ),
			'subject' => $item->get_subject(),
			'message' => $item->get_message(),
			'from'    => $this->prepare_address_for_response( $item->get_from() ),
			'to'      => array_map( [ $this, 'prepare_address_for_response' ], $item->get_to() ),
			'cc'      => array_map( [ $this, 'prepare_address_for_response' ], $item->get_cc() ),
			'bcc'     => array_map( [ $this, 'prepare_address_for_response' ], $item->get_bcc() ),
			'headers' => $item->get_headers(),
			'meta'    => $item->get_all_meta(),
		];

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $item ) );

		return $response;
	}

	private function prepare_address_for_response( Address $address ): array {
		return [
			'email' => $address->get_email(),
			'name'  => $address->get_name(),
		];
	}

	private function prepare_links( Email $email ) {
		return [
			'self' => [
				[
					'href' => rest_url( "{$this->namespace}/{$this->rest_base}/{$email->get_uuid()}" ),
				],
			],
		];
	}

	public function get_collection_params() {
		$params = parent::get_collection_params();

		if ( is_multisite() ) {
			$params['global'] = [
				'type'    => 'boolean',
				'default' => false,
			];
		}

		return $params;
	}

	public function get_item_schema() {
		$address = [
			'type'       => 'object',
			'properties' => [
				'email' => [
					'type'     => 'string',
					'format'   => 'email',
					'required' => true,
				],
				'name'  => [
					'type' => 'string',
				],
			],
		];

		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'wp-mail-debugger-email',
			'type'       => 'object',
			'properties' => [
				'uuid'    => [
					'type'    => 'string',
					'format'  => 'uuid',
					'context' => [ 'view', 'embed', 'edit' ]
				],
				'site_id' => [
					'type'    => 'integer',
					'context' => [ 'view', 'embed', 'edit' ],
				],
				'sent_at' => [
					'type'    => 'string',
					'format'  => 'date-time',
					'context' => [ 'view', 'embed', 'edit' ],
				],
				'subject' => [
					'type'    => 'string',
					'context' => [ 'view', 'embed', 'edit' ],
				],
				'message' => [
					'type'    => 'string',
					'context' => [ 'view', 'edit' ],
				],
				'from'    => [
					'type'       => 'object',
					'properties' => [
						'email' => [
							'type'     => 'string',
							'format'   => 'email',
							'required' => true,
						],
						'name'  => [
							'type' => 'string',
						],
					],
					'context'    => [ 'view', 'embed', 'edit' ],
				],
				'to'      => [
					'type'    => 'array',
					'context' => [ 'view', 'embed', 'edit' ],
					'items'   => $address,
				],
				'cc'      => [
					'type'    => 'array',
					'context' => [ 'view', 'edit' ],
					'items'   => $address,
				],
				'bcc'     => [
					'type'    => 'array',
					'context' => [ 'view', 'edit' ],
					'items'   => $address,
				],
				'headers' => [
					'type'                 => 'object',
					'context'              => [ 'view', 'edit' ],
					'properties'           => [],
					'additionalProperties' => [
						'type' => 'string',
					]
				],
				'meta'    => [
					'type'                 => 'object',
					'context'              => [ 'view', 'edit' ],
					'properties'           => [],
					'additionalProperties' => true,
				],
			],
		];
	}
}
