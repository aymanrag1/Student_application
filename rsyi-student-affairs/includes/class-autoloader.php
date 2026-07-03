<?php
/**
 * Simple PSR-like autoloader for RSYI classes.
 *
 * Convention: class `RSYI_Foo_Bar` → `includes/class-foo-bar.php`
 * Sub-namespaced classes look inside relevant sub-dirs (models, services, etc).
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Autoloader {

	/**
	 * Register the autoloader with SPL.
	 */
	public static function register(): void {
		spl_autoload_register( array( __CLASS__, 'load' ) );
	}

	/**
	 * Load a class file.
	 *
	 * @param string $class_name Fully qualified class name.
	 */
	public static function load( string $class_name ): void {
		if ( strpos( $class_name, 'RSYI_' ) !== 0 ) {
			return;
		}

		$stripped   = substr( $class_name, 5 );
		$file_slug  = strtolower( str_replace( '_', '-', $stripped ) );
		$file_name  = 'class-' . $file_slug . '.php';

		$search_dirs = array(
			RSYI_PLUGIN_DIR . 'includes/',
			RSYI_PLUGIN_DIR . 'includes/models/',
			RSYI_PLUGIN_DIR . 'includes/services/',
			RSYI_PLUGIN_DIR . 'includes/whatsapp/',
			RSYI_PLUGIN_DIR . 'includes/frontend/',
			RSYI_PLUGIN_DIR . 'includes/admin/',
			RSYI_PLUGIN_DIR . 'includes/api/',
			RSYI_PLUGIN_DIR . 'includes/cron/',
		);

		foreach ( $search_dirs as $dir ) {
			$path = $dir . $file_name;
			if ( file_exists( $path ) ) {
				require_once $path;
				return;
			}
		}
	}
}
