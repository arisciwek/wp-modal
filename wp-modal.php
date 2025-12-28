<?php
/**
 * Plugin Name: WP Modal
 * Plugin URI: https://github.com/arisciwek/wp-modal
 * Description: Centralized modal system for WordPress. Provides flexible form, confirmation, and info modals with AJAX support, dynamic buttons, and full accessibility.
 * Version: 0.1.0
 * Author: arisciwek
 * Author URI: https://github.com/arisciwek
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-modal
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package WPModal
 * @version 0.1.0
 * @author arisciwek
 *
 * Path: /wp-modal/wp-modal.php
 *
 * Description: Main plugin file that initializes WP Modal system.
 *              Ported from wp-app-core modal system.
 *
 * Features:
 * - Form modals (Create/Edit with AJAX)
 * - Confirmation modals (Delete/Action confirmations)
 * - Info modals (Success/Error/Warning messages)
 * - AJAX content loading
 * - Dynamic button configuration
 * - Event system
 * - Size variations (small/medium/large)
 * - Full accessibility (ARIA, keyboard navigation)
 * - Responsive design
 *
 * Changelog:
 * 0.1.0 - 2025-11-08
 * - Initial release
 * - Ported from wp-app-core
 * - Clean namespace (WPModal)
 * - New prefix (wpmodal-)
 * - New JavaScript global (WPModal)
 * - AssetController pattern for asset loading
 */

defined('ABSPATH') || exit;

// Define plugin constants
define('WP_MODAL_VERSION', '0.1.0');
define('WP_MODAL_FILE', __FILE__);
define('WP_MODAL_PATH', plugin_dir_path(__FILE__));
define('WP_MODAL_URL', plugin_dir_url(__FILE__));

/**
 * Load plugin textdomain for translations
 */
function wpmodal_load_textdomain() {
    load_plugin_textdomain('wp-modal', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'wpmodal_load_textdomain');

/**
 * Simple autoloader for plugin classes
 */
spl_autoload_register(function ($class) {
    // Only autoload our namespace
    if (strpos($class, 'WPModal\\') !== 0) {
        return;
    }

    // Convert namespace to file path
    // WPModal\Controllers\Assets\AssetController -> Controllers/Assets/AssetController
    $relative_class = substr($class, strlen('WPModal\\'));

    // Convert to file path: Controllers/Assets/AssetController -> Controllers/Assets/AssetController.php
    $file = WP_MODAL_PATH . 'src/' . str_replace('\\', '/', $relative_class) . '.php';

    // Load file if exists
    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Initialize plugin
 */
function wpmodal_init() {
    // Get AssetController instance
    $asset_controller = \WPModal\Controllers\Assets\AssetController::get_instance();

    // Initialize controller
    $asset_controller->init();

    /**
     * Action: After WP Modal initialized
     *
     * @param AssetController $asset_controller Asset controller instance
     */
    do_action('wpmodal_initialized', $asset_controller);
}
// Priority 5 ensures wp-modal loads BEFORE wp-datatable (priority 10)
// This way admin_enqueue_scripts hooks are registered in correct order
add_action('plugins_loaded', 'wpmodal_init', 5);

/**
 * Activation hook
 */
function wpmodal_activate() {
    // Set default options
    add_option('wpmodal_version', WP_MODAL_VERSION);

    /**
     * Action: On plugin activation
     */
    do_action('wpmodal_activated');

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wpmodal_activate');

/**
 * Deactivation hook
 */
function wpmodal_deactivate() {
    /**
     * Action: On plugin deactivation
     */
    do_action('wpmodal_deactivated');

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wpmodal_deactivate');
