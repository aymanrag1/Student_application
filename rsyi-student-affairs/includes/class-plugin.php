<?php
/**
 * Main Plugin Class - Singleton bootstrap.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

final class RSYI_Plugin {

	private static ?RSYI_Plugin $instance = null;

	public static function instance(): RSYI_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->load_textdomain();
		$this->init_hooks();
	}

	private function load_textdomain(): void {
		load_plugin_textdomain(
			'rsyi-student-affairs',
			false,
			dirname( RSYI_PLUGIN_BASENAME ) . '/languages'
		);
	}

	private function init_hooks(): void {
		add_action( 'init', array( $this, 'init' ) );

		if ( is_admin() ) {
			RSYI_Admin::register();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

		RSYI_Stage_One_Handler::register();
		RSYI_Stage_Two_Handler::register();
		RSYI_Candidate_Detail::register();
		RSYI_Interviews_Page::register();
		RSYI_Medical_Page::register();
		RSYI_Export_Service::register();
		RSYI_REST_Controller::register();
	}

	public function init(): void {
		RSYI_Shortcodes::register();
		RSYI_Notification_Service::bootstrap();
		RSYI_Reminder_Cron::register();

		// REST API routes registration will be wired in Phase 15.

		/**
		 * Fires after RSYI plugin is initialized.
		 */
		do_action( 'rsyi_initialized' );
	}

	public function enqueue_admin_assets( string $hook ): void {
		if ( strpos( $hook, 'rsyi' ) === false ) {
			return;
		}

		$is_rtl = is_rtl();
		$css_file = $is_rtl ? 'admin-rtl.css' : 'admin.css';

		wp_enqueue_style(
			'rsyi-admin',
			RSYI_PLUGIN_URL . 'assets/css/' . $css_file,
			array(),
			RSYI_VERSION
		);
	}

	public function enqueue_frontend_assets(): void {
		global $post;

		if ( ! $post || ! has_shortcode( $post->post_content, 'rsyi_application_form' ) ) {
			return;
		}

		$is_rtl = is_rtl();
		$css_file = $is_rtl ? 'frontend-rtl.css' : 'frontend.css';

		wp_enqueue_style(
			'rsyi-frontend',
			RSYI_PLUGIN_URL . 'assets/css/' . $css_file,
			array(),
			RSYI_VERSION
		);

		wp_enqueue_script(
			'rsyi-stage-one',
			RSYI_PLUGIN_URL . 'assets/js/frontend-stage-one.js',
			array(),
			RSYI_VERSION,
			true
		);
	}

	private function __clone() {}

	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton.' );
	}
}
