<?php
/**
 * REST API - external system integration.
 *
 * Base URL: /wp-json/rsyi/v1/
 * Auth: Bearer token stored in option `rsyi_api_token`.
 *
 * Endpoints:
 *  GET  /students                     - list with filters
 *  GET  /students/{id}                - single student detail
 *  GET  /students/{id}/documents      - documents metadata (no direct file access)
 *  GET  /statistics                   - aggregated counts by status
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_REST_Controller {

	const NAMESPACE = 'rsyi/v1';

	public static function register(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	public static function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/students',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'list_students' ),
				'permission_callback' => array( __CLASS__, 'check_auth' ),
				'args'                => array(
					'status'   => array( 'type' => 'string' ),
					'per_page' => array( 'type' => 'integer', 'default' => 50 ),
					'page'     => array( 'type' => 'integer', 'default' => 1 ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/students/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_student' ),
				'permission_callback' => array( __CLASS__, 'check_auth' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/students/(?P<id>\d+)/documents',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_documents' ),
				'permission_callback' => array( __CLASS__, 'check_auth' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/statistics',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_statistics' ),
				'permission_callback' => array( __CLASS__, 'check_auth' ),
			)
		);
	}

	public static function check_auth( WP_REST_Request $request ): bool {
		$header = $request->get_header( 'authorization' );
		if ( ! $header || stripos( $header, 'Bearer ' ) !== 0 ) {
			return false;
		}

		$provided = trim( substr( $header, 7 ) );
		$expected = get_option( 'rsyi_api_token', '' );

		if ( ! $expected ) {
			return false;
		}

		return hash_equals( (string) $expected, $provided );
	}

	public static function list_students( WP_REST_Request $request ): WP_REST_Response {
		$args = array(
			'status'   => $request->get_param( 'status' ) ?: null,
			'per_page' => (int) $request->get_param( 'per_page' ),
			'page'     => (int) $request->get_param( 'page' ),
		);

		$students = RSYI_Student::list( $args );
		$total    = RSYI_Student::count( $args );

		$data = array_map( array( __CLASS__, 'transform_student' ), $students );

		$response = new WP_REST_Response(
			array(
				'data'       => $data,
				'total'      => $total,
				'per_page'   => (int) $args['per_page'],
				'page'       => (int) $args['page'],
				'total_pages' => (int) ceil( $total / max( 1, (int) $args['per_page'] ) ),
			),
			200
		);

		return $response;
	}

	public static function get_student( WP_REST_Request $request ): WP_REST_Response {
		$id      = (int) $request['id'];
		$student = RSYI_Student::get( $id );

		if ( ! $student ) {
			return new WP_REST_Response( array( 'error' => 'not_found' ), 404 );
		}

		$data = self::transform_student( $student );
		$data['interview']       = RSYI_Interview::get_for_student( $id );
		$data['medical']         = RSYI_Medical_Exam::get_for_student( $id );
		$data['language_test']   = RSYI_Language_Test::get_for_student( $id );
		$data['history']         = RSYI_Status_Log::get_history( $id );

		return new WP_REST_Response( $data, 200 );
	}

	public static function get_documents( WP_REST_Request $request ): WP_REST_Response {
		$id = (int) $request['id'];
		$documents = RSYI_Document::all_for_student( $id );

		$data = array();
		foreach ( $documents as $doc ) {
			$data[] = array(
				'id'            => (int) $doc['id'],
				'document_type' => $doc['document_type'],
				'file_name'     => $doc['file_name'],
				'file_size'     => (int) $doc['file_size'],
				'mime_type'     => $doc['mime_type'],
				'uploaded_at'   => $doc['uploaded_at'],
			);
		}

		return new WP_REST_Response( array( 'data' => $data ), 200 );
	}

	public static function get_statistics( WP_REST_Request $request ): WP_REST_Response {
		return new WP_REST_Response( RSYI_Reports_Page::compute_stats(), 200 );
	}

	private static function transform_student( array $s ): array {
		return array(
			'id'                 => (int) $s['id'],
			'application_code'   => $s['application_code'],
			'full_name'          => $s['full_name'],
			'national_id'        => $s['national_id'],
			'date_of_birth'      => $s['date_of_birth'],
			'email'              => $s['email'],
			'mobile_1'           => $s['mobile_1'],
			'whatsapp'           => $s['whatsapp'],
			'nationality'        => $s['nationality'],
			'high_school_type'   => $s['high_school_type'],
			'high_school_track'  => $s['high_school_track'],
			'military_status'    => $s['military_status'],
			'status'             => $s['status'],
			'status_label'       => RSYI_Constants::status_label( $s['status'] ),
			'stage_one_submitted_at' => $s['stage_one_submitted_at'],
			'stage_two_submitted_at' => $s['stage_two_submitted_at'],
		);
	}
}
