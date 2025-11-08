<?php
/**
 * Asset Controller
 *
 * @package     WPModal
 * @subpackage  Controllers/Assets
 * @version     0.1.0
 * @author      arisciwek
 *
 * Path: /wp-modal/src/Controllers/Assets/AssetController.php
 *
 * Description: Simple asset controller untuk loading modal CSS dan JavaScript.
 *              Menangani enqueuing styles dan scripts di admin area.
 *
 * Responsibilities:
 * - Enqueue modal CSS
 * - Enqueue modal JavaScript
 * - Localize script untuk i18n dan config
 * - Hook ke WordPress admin_enqueue_scripts
 *
 * Usage:
 * ```php
 * $asset_controller = AssetController::get_instance();
 * $asset_controller->init();
 * ```
 *
 * Changelog:
 * 0.1.0 - 2025-11-08
 * - Initial implementation
 * - Singleton pattern
 * - Simple asset loading
 * - i18n support
 */

namespace WPModal\Controllers\Assets;

defined('ABSPATH') || exit;

class AssetController {
    /**
     * Singleton instance
     *
     * @var AssetController|null
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return AssetController
     */
    public static function get_instance(): AssetController {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - private untuk Singleton
     */
    private function __construct() {
        // Hook into WordPress admin asset enqueue
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Initialize controller
     *
     * @return void
     */
    public function init(): void {
        // Render modal template in admin footer
        add_action('admin_footer', [$this, 'render_modal_template']);

        /**
         * Action: After AssetController initialized
         *
         * @param AssetController $this Controller instance
         */
        do_action('wpmodal_asset_controller_init', $this);
    }

    /**
     * Enqueue modal assets
     *
     * @return void
     */
    public function enqueue_assets(): void {
        /**
         * Action: Before enqueuing assets
         */
        do_action('wpmodal_before_enqueue_assets');

        // Enqueue CSS
        wp_enqueue_style(
            'wp-modal',
            WP_MODAL_URL . 'assets/css/wp-modal.css',
            [],
            WP_MODAL_VERSION,
            'all'
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'wp-modal',
            WP_MODAL_URL . 'assets/js/wp-modal.js',
            ['jquery'],
            WP_MODAL_VERSION,
            true
        );

        // Localize script untuk i18n dan config
        wp_localize_script('wp-modal', 'wpModalConfig', [
            'version' => WP_MODAL_VERSION,
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpmodal_nonce'),
            'i18n' => [
                'loading' => __('Loading...', 'wp-modal'),
                'close' => __('Close', 'wp-modal'),
                'cancel' => __('Cancel', 'wp-modal'),
                'save' => __('Save', 'wp-modal'),
                'confirm' => __('Confirm', 'wp-modal'),
                'ok' => __('OK', 'wp-modal'),
                'error' => __('Error', 'wp-modal'),
                'success' => __('Success', 'wp-modal'),
            ]
        ]);

        /**
         * Action: After assets enqueued
         */
        do_action('wpmodal_after_enqueue_assets');
    }

    /**
     * Render modal template in footer
     *
     * @return void
     */
    public function render_modal_template(): void {
        // Render template (autoloaded via namespace)
        \WPModal\Views\Modal\ModalTemplate::render();
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception('Cannot unserialize singleton');
    }
}
