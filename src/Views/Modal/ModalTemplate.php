<?php
/**
 * WP Modal - Modal Template View
 *
 * @package     WPModal
 * @subpackage  Views/Modal
 * @version     0.1.0
 * @author      arisciwek
 *
 * Path: /wp-modal/src/Views/Modal/ModalTemplate.php
 *
 * Description: Single flexible modal template for all modal types.
 *              Supports form modals, confirmation modals, and info modals.
 *              Content injected via AJAX or hooks.
 *              Buttons configured dynamically in footer.
 *
 * Modal Types:
 * - form: Create/Edit forms with Cancel + Submit buttons
 * - confirmation: Delete/Action confirmations with Cancel + Confirm buttons
 * - info: Success/Error/Warning messages with OK button
 *
 * Features:
 * - Single template for all types
 * - Hook-based content injection
 * - AJAX content loading
 * - Dynamic footer buttons
 * - Size options (small/medium/large)
 * - ESC key to close
 * - Click overlay to close
 * - Loading state
 * - Accessibility (ARIA)
 *
 * Usage:
 * ModalTemplate::render() - Output modal HTML
 * JavaScript API (WPModal) controls visibility and content
 *
 * Changelog:
 * 0.1.0 - 2025-11-08
 * - Moved to MVC structure (src/Views/Modal/)
 * - Changed class name to ModalTemplate (PSR standard)
 * - Namespace: WPModal\Views\Modal
 */

namespace WPModal\Views\Modal;

defined('ABSPATH') || exit;

class ModalTemplate {

    /**
     * Render modal template
     *
     * Outputs hidden modal HTML structure.
     * Modal is shown/hidden and populated via JavaScript.
     *
     * @return void
     */
    public static function render(): void {
        ?>
        <!-- WP Modal - Centralized Modal Template -->
        <div id="wpmodal"
             class="wpmodal"
             style="display:none"
             data-modal-type=""
             role="dialog"
             aria-modal="true"
             aria-labelledby="wpmodal-title"
             aria-hidden="true">

            <!-- Overlay/Backdrop -->
            <div class="wpmodal-overlay" aria-hidden="true"></div>

            <!-- Modal Container -->
            <div class="wpmodal-container" role="document">

                <!-- Header -->
                <div class="wpmodal-header">
                    <h2 id="wpmodal-title" class="wpmodal-title"></h2>
                    <button type="button"
                            class="wpmodal-close"
                            aria-label="<?php esc_attr_e('Close modal', 'wp-modal'); ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="wpmodal-body">
                    <!-- Loading state -->
                    <div class="wpmodal-loading" style="display:none">
                        <div class="wpmodal-spinner"></div>
                        <p><?php esc_html_e('Loading...', 'wp-modal'); ?></p>
                    </div>

                    <!-- Content injected here via AJAX or JavaScript -->
                    <div class="wpmodal-content">
                        <!-- Dynamic content -->
                    </div>
                </div>

                <!-- Footer -->
                <div class="wpmodal-footer">
                    <!-- Buttons injected here via JavaScript -->
                </div>

            </div><!-- .wpmodal-container -->

        </div><!-- #wpmodal -->
        <?php
    }

    /**
     * Render button HTML
     *
     * Helper method to render a single button.
     * Used by JavaScript or can be called directly.
     *
     * @param array $button Button configuration
     * @return string Button HTML
     */
    public static function render_button(array $button): string {
        $defaults = [
            'id' => '',
            'label' => 'Button',
            'class' => 'button',
            'type' => 'button',
            'disabled' => false,
            'data' => []
        ];

        $button = array_merge($defaults, $button);

        $classes = ['wpmodal-btn', $button['class']];
        $attrs = [];

        if ($button['id']) {
            $attrs[] = sprintf('id="%s"', esc_attr($button['id']));
        }

        $attrs[] = sprintf('type="%s"', esc_attr($button['type']));
        $attrs[] = sprintf('class="%s"', esc_attr(implode(' ', $classes)));

        if ($button['disabled']) {
            $attrs[] = 'disabled';
        }

        foreach ($button['data'] as $key => $value) {
            $attrs[] = sprintf('data-%s="%s"', esc_attr($key), esc_attr($value));
        }

        return sprintf(
            '<button %s>%s</button>',
            implode(' ', $attrs),
            esc_html($button['label'])
        );
    }

    /**
     * Get default buttons for modal type
     *
     * Returns default button configuration for each modal type.
     *
     * @param string $type Modal type (form|confirmation|info)
     * @return array Button configuration
     */
    public static function get_default_buttons(string $type): array {
        $buttons = [];

        switch ($type) {
            case 'form':
                $buttons = [
                    'cancel' => [
                        'id' => 'wpmodal-cancel',
                        'label' => __('Cancel', 'wp-modal'),
                        'class' => 'button',
                        'data' => ['action' => 'cancel']
                    ],
                    'submit' => [
                        'id' => 'wpmodal-submit',
                        'label' => __('Save', 'wp-modal'),
                        'class' => 'button button-primary',
                        'type' => 'submit',
                        'data' => ['action' => 'submit']
                    ]
                ];
                break;

            case 'confirmation':
                $buttons = [
                    'cancel' => [
                        'id' => 'wpmodal-cancel',
                        'label' => __('Cancel', 'wp-modal'),
                        'class' => 'button',
                        'data' => ['action' => 'cancel']
                    ],
                    'confirm' => [
                        'id' => 'wpmodal-confirm',
                        'label' => __('Confirm', 'wp-modal'),
                        'class' => 'button button-primary',
                        'data' => ['action' => 'confirm']
                    ]
                ];
                break;

            case 'info':
                $buttons = [
                    'ok' => [
                        'id' => 'wpmodal-ok',
                        'label' => __('OK', 'wp-modal'),
                        'class' => 'button button-primary',
                        'data' => ['action' => 'close']
                    ]
                ];
                break;

            default:
                // Generic close button
                $buttons = [
                    'close' => [
                        'id' => 'wpmodal-close-btn',
                        'label' => __('Close', 'wp-modal'),
                        'class' => 'button',
                        'data' => ['action' => 'close']
                    ]
                ];
        }

        /**
         * Filter default buttons for modal type
         *
         * @param array $buttons Button configuration
         * @param string $type Modal type
         */
        return apply_filters('wpmodal_default_buttons', $buttons, $type);
    }
}
