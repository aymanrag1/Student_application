<?php
/**
 * UltraMsg.com WhatsApp driver.
 *
 * Config: rsyi_whatsapp_config = ['instance_id' => '...', 'token' => '...']
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_UltraMsg_Driver implements RSYI_WhatsApp_Driver_Interface {

	public function send( string $to, string $message ): array {
		$config = get_option( 'rsyi_whatsapp_config', array() );
		$instance = $config['instance_id'] ?? '';
		$token    = $config['token'] ?? '';

		if ( empty( $instance ) || empty( $token ) ) {
			return array( 'success' => false, 'response' => '', 'error' => 'UltraMsg not configured' );
		}

		$url = "https://api.ultramsg.com/{$instance}/messages/chat";
		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'token' => $token,
					'to'    => $to,
					'body'  => $message,
				),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array( 'success' => false, 'response' => '', 'error' => $response->get_error_message() );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		return array(
			'success'  => ( $code >= 200 && $code < 300 ),
			'response' => $body,
			'error'    => ( $code >= 200 && $code < 300 ) ? '' : "HTTP {$code}",
		);
	}

	public function get_name(): string {
		return 'ultramsg';
	}
}
