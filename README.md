# WP Modal

**Version:** 0.1.0
**Author:** arisciwek
**License:** GPL v2 or later

Centralized modal system for WordPress. Provides flexible form, confirmation, and info modals with AJAX support, dynamic buttons, and full accessibility.

## Features

✅ **Form Modals** - Create/Edit forms with AJAX loading
✅ **Confirmation Modals** - Delete/Action confirmations with danger styling
✅ **Info Modals** - Success/Error/Warning messages with icons
✅ **AJAX Content Loading** - Dynamic content from server
✅ **Dynamic Buttons** - Fully configurable footer buttons
✅ **Event System** - Custom events (opened/closed/submit)
✅ **Size Options** - Small/Medium/Large variations
✅ **Accessibility** - ARIA attributes, keyboard navigation
✅ **Responsive** - Mobile-friendly design
✅ **Auto-close** - Configurable auto-close for info modals

## Installation

1. Upload plugin to `/wp-content/plugins/wp-modal/`
2. Activate through WordPress admin
3. Modal system is now available globally in admin

## Quick Start

### Basic Form Modal

```javascript
WPModal.show({
    type: 'form',
    title: 'Add New Item',
    bodyUrl: ajaxurl + '?action=get_item_form',
    size: 'medium',
    onSubmit: function(formData, $form) {
        // Handle form submission
        $.ajax({
            url: ajaxurl,
            data: formData,
            success: function(response) {
                WPModal.hide();
                // Show success message
            }
        });
    }
});
```

### Confirmation Modal

```javascript
WPModal.confirm({
    title: 'Delete Item?',
    message: 'Are you sure you want to delete this item?',
    danger: true,
    onConfirm: function() {
        // Perform delete action
    }
});
```

### Info/Success Modal

```javascript
WPModal.info({
    infoType: 'success',
    title: 'Success',
    message: 'Item saved successfully!',
    autoClose: 3000
});
```

## API Reference

### `WPModal.show(config)`

Show modal with custom configuration.

**Parameters:**
```javascript
{
    type: 'form',              // 'form'|'confirmation'|'info'
    title: 'Modal Title',      // Modal title text
    body: '<p>HTML</p>',       // Direct HTML content (optional)
    bodyUrl: 'url',            // AJAX URL to load content (optional)
    size: 'medium',            // 'small'|'medium'|'large'
    buttons: {...},            // Button configuration (optional)
    preventClose: false,       // Prevent close on overlay/ESC
    autoClose: 0,              // Auto-close in ms (0 = disabled)
    onSubmit: function() {},   // Submit callback
    onClose: function() {},    // Close callback
    onConfirm: function() {}   // Confirm callback
}
```

### `WPModal.hide()`

Hide and reset modal.

### `WPModal.setContent(html)`

Update modal body content.

### `WPModal.setTitle(title)`

Update modal title.

### `WPModal.loading(show)`

Show/hide loading state.

**Parameters:**
- `show` (boolean): true to show loading, false to hide

### `WPModal.confirm(config)`

Shortcut for confirmation modal.

**Parameters:**
```javascript
{
    title: 'Confirm Action',
    message: 'Are you sure?',
    danger: true,              // Apply danger styling
    confirmLabel: 'Confirm',   // Custom confirm button label
    onConfirm: function() {}
}
```

### `WPModal.info(config)`

Shortcut for info modal.

**Parameters:**
```javascript
{
    infoType: 'success',       // 'success'|'error'|'warning'|'info'
    title: 'Information',
    message: 'Message text',
    autoClose: 3000            // Auto-close in ms
}
```

## Events

### `wpmodal:modal-opened`

Fired when modal is shown.

```javascript
$(document).on('wpmodal:modal-opened', function(event, config) {
    console.log('Modal opened with config:', config);
});
```

### `wpmodal:modal-closed`

Fired when modal is hidden.

```javascript
$(document).on('wpmodal:modal-closed', function(event) {
    console.log('Modal closed');
});
```

### `wpmodal:modal-submit`

Fired when submit button clicked (form modals).

```javascript
$(document).on('wpmodal:modal-submit', function(event, formData) {
    console.log('Form submitted with data:', formData);
});
```

## Size Options

### Small (400px)
Best for: Confirmations, simple info messages

```javascript
WPModal.show({ size: 'small', ... });
```

### Medium (600px) - Default
Best for: Standard forms, detailed messages

```javascript
WPModal.show({ size: 'medium', ... });
```

### Large (800px)
Best for: Complex forms, multi-section content

```javascript
WPModal.show({ size: 'large', ... });
```

## Plugin Integration Example

### PHP: AJAX Handler

```php
// In your plugin controller
public function handle_get_item_form() {
    check_ajax_referer('my_nonce', 'nonce');

    $item_id = $_GET['id'] ?? 0;

    if ($item_id) {
        $item = MyModel::get_by_id($item_id);
        include 'forms/edit-item-form.php';
    } else {
        include 'forms/create-item-form.php';
    }

    wp_die();
}

add_action('wp_ajax_get_item_form', [$this, 'handle_get_item_form']);
```

### JavaScript: Modal Trigger

```javascript
$(document).on('click', '.add-item-btn', function(e) {
    e.preventDefault();

    WPModal.show({
        type: 'form',
        title: 'Add New Item',
        bodyUrl: ajaxurl + '?action=get_item_form',
        size: 'medium',
        onSubmit: function(formData, $form) {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        WPModal.info({
                            infoType: 'success',
                            message: 'Item saved successfully!',
                            autoClose: 3000
                        });
                        // Reload table or update UI
                    }
                }
            });
        }
    });
});
```

## Styling

The plugin provides complete CSS styling. You can customize by overriding CSS variables or classes:

```css
/* Custom modal width */
.wpmodal-container.wpmodal-large {
    max-width: 1000px;
}

/* Custom primary button color */
.wpmodal-footer .button-primary {
    background: #your-color;
}
```

## Hooks & Filters

### Actions

```php
// After plugin initialized
do_action('wpmodal_initialized', $asset_controller);

// Before/After asset enqueue
do_action('wpmodal_before_enqueue_assets');
do_action('wpmodal_after_enqueue_assets');

// On activation/deactivation
do_action('wpmodal_activated');
do_action('wpmodal_deactivated');
```

### Filters

```php
// Modify default buttons for modal types
add_filter('wpmodal_default_buttons', function($buttons, $type) {
    // Customize buttons
    return $buttons;
}, 10, 2);
```

## Requirements

- WordPress 5.8+
- PHP 7.4+
- jQuery (bundled with WordPress)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Changelog

### 0.1.0 - 2025-11-08
- Initial release
- Ported from wp-app-core
- Clean namespace (WPModal)
- New prefix (wpmodal-)
- New JavaScript global (WPModal)
- AssetController pattern for asset loading

## Credits

Developed by **arisciwek**

## License

GPL v2 or later

---

For issues or questions, please visit [GitHub Issues](https://github.com/arisciwek/wp-modal/issues)
