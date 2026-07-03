<?php
/**
 * Plugin Name:       RSYI Student Affairs
 * Plugin URI:        https://redseayachtinstitute.com
 * Description:       نظام إدارة شئون طلاب معهد البحر الأحمر لليخوت - تقديم المنحة، الفلترة، المقابلات، الفحص الطبي، تحديد المستوى، وربط بسيستم خارجي.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Red Sea Yacht Institute
 * Author URI:        https://redseayachtinstitute.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rsyi-student-affairs
 * Domain Path:       /languages
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

define( 'RSYI_VERSION', '0.1.0' );
define( 'RSYI_PLUGIN_FILE', __FILE__ );
define( 'RSYI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RSYI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RSYI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'RSYI_DB_VERSION', '0.1.0' );
define( 'RSYI_UPLOADS_SUBDIR', 'rsyi-docs' );

require_once RSYI_PLUGIN_DIR . 'includes/class-autoloader.php';
RSYI_Autoloader::register();

require_once RSYI_PLUGIN_DIR . 'includes/class-activator.php';
require_once RSYI_PLUGIN_DIR . 'includes/class-deactivator.php';

register_activation_hook( __FILE__, array( 'RSYI_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'RSYI_Deactivator', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'RSYI_Plugin', 'instance' ) );
