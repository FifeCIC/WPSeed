# Unified Feature Example

## Overview

WPSeed includes a unified feature example that demonstrates how to create content that works across **all WordPress display contexts**:

1. **Dashboard Widget** - Admin dashboard
2. **Gutenberg Block** - Block editor
3. **Shortcode** - Classic editor & anywhere
4. **Sidebar Widget** - Widget areas

## Core Concept

All implementations use the **same core rendering function**: `WPSeed_Unified_Feature::render_content()`

This ensures:
- Consistent output across all contexts
- Single source of truth
- Easy maintenance
- DRY principle

## Usage

### 1. Dashboard Widget

Automatically appears on WordPress dashboard for administrators.

**Location**: Dashboard → WPSeed Unified Feature

### 2. Shortcode

```
[wpseed_feature]
[wpseed_feature title="Custom Title"]
[wpseed_feature show_icon="no"]
[wpseed_feature show_stats="no"]
```

**Parameters**:
- `title` - Custom title (default: "WPSeed Feature")
- `show_icon` - yes/no (default: yes)
- `show_stats` - yes/no (default: yes)

### 3. Sidebar Widget

**Location**: Appearance → Widgets → WPSeed Unified Feature

Drag to any widget area. Configure:
- Title
- Show Icon (checkbox)
- Show Stats (checkbox)

### 4. Gutenberg Block

**Location**: Block Editor → Add Block → WPSeed → Unified Feature

**Block Attributes**:
- Title (text input)
- Show Icon (toggle)
- Show Stats (toggle)

## Implementation Pattern

```php
class Your_Unified_Feature {
    
    // Core rendering - used by ALL implementations
    public static function render_content( $args = array() ) {
        // Your rendering logic here
        return $output;
    }
    
    // Dashboard Widget
    public function render_dashboard_widget() {
        echo self::render_content();
    }
    
    // Shortcode
    public function render_shortcode( $atts ) {
        return self::render_content( $atts );
    }
    
    // Block
    public function render_block( $attributes ) {
        return self::render_content( $attributes );
    }
    
    // Sidebar Widget
    // Use WP_Widget class, call render_content() in widget() method
}
```

## Benefits

1. **Consistency** - Same output everywhere
2. **Maintainability** - Update once, applies everywhere
3. **Flexibility** - Users choose their preferred method
4. **Accessibility** - Works in all WordPress contexts
5. **Future-Proof** - Easy to add new display methods

## Customization

Extend the pattern for your features:

```php
// Your feature
class My_Custom_Feature {
    public static function render_content( $args ) {
        // Your custom logic
        return '<div>My Content</div>';
    }
}

// Register everywhere
add_shortcode( 'my_feature', array( 'My_Custom_Feature', 'render_content' ) );
// Add dashboard widget, block, sidebar widget...
```

## Best Practices

1. **Single Render Function** - All implementations call one function
2. **Consistent Args** - Use same parameter names across contexts
3. **Sensible Defaults** - Provide defaults for all parameters
4. **Escape Output** - Always escape for security
5. **Style Inline** - Include minimal inline styles for portability
6. **Test All Contexts** - Verify in dashboard, editor, frontend, widgets

## jQuery UI Settings Gallery

WPSeed includes a complete gallery of all jQuery UI components supported by WordPress core:

**Location**: WPSeed → Settings → jQuery UI Gallery

**Components**:
- Datepicker
- Slider
- Progressbar
- Autocomplete
- Accordion
- Tabs
- Dialog
- Sortable
- Spinner

All components are properly enqueued and initialized. Use as reference for your own implementations.
