<?php
/**
 * Student model - CRUD operations for rsyi_students table.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Student {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_students';
	}

	public static function create( array $data ): int {
		global $wpdb;

		$now = current_time( 'mysql' );

		$defaults = array(
			'status'                 => RSYI_Constants::STATUS_SUBMITTED_STAGE_1,
			'reminder_stopped'       => 0,
			'reminder_count'         => 0,
			'stage_one_submitted_at' => $now,
			'created_at'             => $now,
			'updated_at'             => $now,
		);

		$row = wp_parse_args( $data, $defaults );

		if ( empty( $row['application_code'] ) ) {
			$row['application_code'] = self::generate_application_code();
		}

		$result = $wpdb->insert( self::table(), $row );
		if ( false === $result ) {
			return 0;
		}

		$id = (int) $wpdb->insert_id;

		RSYI_Status_Log::log( $id, null, $row['status'], null, __( 'تقديم أولي', 'rsyi-student-affairs' ) );

		return $id;
	}

	public static function update( int $id, array $data ): bool {
		global $wpdb;

		$data['updated_at'] = current_time( 'mysql' );

		return false !== $wpdb->update( self::table(), $data, array( 'id' => $id ) );
	}

	public static function get( int $id ): ?array {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . self::table() . " WHERE id = %d", $id ), ARRAY_A );
		return $row ?: null;
	}

	public static function get_by_national_id( string $national_id ): ?array {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM " . self::table() . " WHERE national_id = %s", $national_id ),
			ARRAY_A
		);
		return $row ?: null;
	}

	public static function get_by_application_code( string $code ): ?array {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM " . self::table() . " WHERE application_code = %s", $code ),
			ARRAY_A
		);
		return $row ?: null;
	}

	public static function list( array $args = array() ): array {
		global $wpdb;

		$defaults = array(
			'status'   => null,
			'search'   => null,
			'orderby'  => 'stage_one_submitted_at',
			'order'    => 'DESC',
			'per_page' => 20,
			'page'     => 1,
		);

		$args = wp_parse_args( $args, $defaults );

		$where  = array( '1=1' );
		$params = array();

		if ( ! empty( $args['status'] ) ) {
			$where[]  = 'status = %s';
			$params[] = $args['status'];
		}

		if ( ! empty( $args['search'] ) ) {
			$search   = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$where[]  = '(full_name LIKE %s OR national_id LIKE %s OR email LIKE %s OR application_code LIKE %s)';
			array_push( $params, $search, $search, $search, $search );
		}

		$where_sql = implode( ' AND ', $where );
		$orderby   = esc_sql( $args['orderby'] );
		$order     = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
		$offset    = ( max( 1, (int) $args['page'] ) - 1 ) * (int) $args['per_page'];
		$per_page  = (int) $args['per_page'];

		$sql = "SELECT * FROM " . self::table() . " WHERE {$where_sql} ORDER BY {$orderby} {$order} LIMIT {$per_page} OFFSET {$offset}";

		if ( ! empty( $params ) ) {
			$sql = $wpdb->prepare( $sql, $params );
		}

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	public static function count( array $args = array() ): int {
		global $wpdb;

		$where  = array( '1=1' );
		$params = array();

		if ( ! empty( $args['status'] ) ) {
			$where[]  = 'status = %s';
			$params[] = $args['status'];
		}

		$where_sql = implode( ' AND ', $where );
		$sql       = "SELECT COUNT(*) FROM " . self::table() . " WHERE {$where_sql}";
		if ( ! empty( $params ) ) {
			$sql = $wpdb->prepare( $sql, $params );
		}

		return (int) $wpdb->get_var( $sql );
	}

	public static function national_id_exists( string $national_id ): bool {
		return self::get_by_national_id( $national_id ) !== null;
	}

	public static function generate_application_code(): string {
		$year   = gmdate( 'Y' );
		$prefix = "RSYI-{$year}-";

		global $wpdb;
		$table = self::table();

		$max = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT MAX(CAST(SUBSTRING(application_code, %d) AS UNSIGNED)) FROM {$table} WHERE application_code LIKE %s",
				strlen( $prefix ) + 1,
				$prefix . '%'
			)
		);

		$next = ( (int) $max ) + 1;
		return sprintf( '%s%05d', $prefix, $next );
	}

	public static function change_status( int $id, string $new_status, ?int $user_id = null, ?string $reason = null ): bool {
		$student = self::get( $id );
		if ( ! $student ) {
			return false;
		}

		$from = $student['status'];
		if ( $from === $new_status ) {
			return true;
		}

		$updated = self::update( $id, array( 'status' => $new_status ) );
		if ( ! $updated ) {
			return false;
		}

		RSYI_Status_Log::log( $id, $from, $new_status, $user_id, $reason );

		do_action( 'rsyi_student_status_changed', $id, $from, $new_status, $user_id );

		return true;
	}
}
