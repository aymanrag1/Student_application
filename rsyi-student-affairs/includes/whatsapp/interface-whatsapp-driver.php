<?php
/**
 * WhatsApp driver contract.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

interface RSYI_WhatsApp_Driver_Interface {

	/**
	 * Send a text message.
	 *
	 * @param string $to      International phone number (e.g. 201234567890).
	 * @param string $message Plain text message body.
	 * @return array {
	 *     @type bool   $success  True if provider accepted.
	 *     @type string $response Raw provider response (JSON).
	 *     @type string $error    Error message if failed.
	 * }
	 */
	public function send( string $to, string $message ): array;

	public function get_name(): string;
}
