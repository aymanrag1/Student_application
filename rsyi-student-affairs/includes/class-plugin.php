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
			add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	public function init(): void {
		// Shortcodes registration will be wired here in Phase 2.
		// REST API routes registration will be wired here in Phase 15.
		// Cron hooks will be wired here in Phase 13.

		/**
		 * Fires after RSYI plugin is initialized.
		 */
		do_action( 'rsyi_initialized' );
	}

	public function register_admin_pages(): void {
		add_menu_page(
			__( 'شئون الطلاب', 'rsyi-student-affairs' ),
			__( 'شئون الطلاب', 'rsyi-student-affairs' ),
			'manage_options',
			'rsyi-candidates',
			array( $this, 'render_dashboard_placeholder' ),
			'dashicons-groups',
			25
		);
	}

	public function render_dashboard_placeholder(): void {
		echo '<div class="wrap"><h1>' . esc_html__( 'شئون طلاب معهد البحر الأحمر لليخوت', 'rsyi-student-affairs' ) . '</h1>';
		echo '<p>' . esc_html__( 'قيد التطوير - Phase 5', 'rsyi-student-affairs' ) . '</p></div>';
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
	}

	private function __clone() {}

	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton.' );
	}
}
