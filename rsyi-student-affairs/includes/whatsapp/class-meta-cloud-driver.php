<?php
/**
 * Meta (Facebook) WhatsApp Cloud API driver.
 *
 * Config: rsyi_whatsapp_config = ['phone_number_id' => '...', 'access_token' => '...']
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Meta_Cloud_Driver implements RSYI_WhatsApp_Driver_Interface {

	public function send( string $to, string $message ): array {
		$config = get_option( 'rsyi_whatsapp_config', array() );
		$phone_id = $config['phone_number_id'] ?? '';
		$token    = $config['access_token'] ?? '';

		if ( empty( $phone_id ) || empty( $token ) ) {
			return array( 'success' => false, 'response' => '', 'error' => 'Meta Cloud API not configured' );
		}

		$url = "https://graph.facebook.com/v20.0/{$phone_id}/messages";
		$payload = array(
			'messaging_product' => 'whatsapp',
			'to'                => $to,
			'type'              => 'text',
			'text'              => array( 'body' => $message ),
		);

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode( $payload ),
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
		return 'meta_cloud';
	}
}
