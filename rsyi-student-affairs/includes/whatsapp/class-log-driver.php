<?php
/**
 * Log-only WhatsApp driver (no external send).
 * Default driver during development or before configuring a real provider.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Log_Driver implements RSYI_WhatsApp_Driver_Interface {

	public function send( string $to, string $message ): array {
		error_log( sprintf( '[RSYI WhatsApp LOG] To: %s | Msg: %s', $to, $message ) );
		return array(
			'success'  => true,
			'response' => wp_json_encode( array( 'driver' => 'log', 'to' => $to, 'noted' => true ) ),
			'error'    => '',
		);
	}

	public function get_name(): string {
		return 'log';
	}
}
