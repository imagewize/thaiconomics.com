<?php
/**
 * Block Extensions
 * 
 * Extend core blocks with additional functionality.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue block extension assets.
 */
function moiraine_enqueue_block_extensions() {
    // Make sure the directory exists
    $js_path = get_theme_file_path('/assets/js/block-extensions');
    if (!file_exists($js_path)) {
        wp_mkdir_p($js_path);
    }
    
    // Create file path and URL
    $js_file_path = $js_path . '/post-excerpt.js';
    $asset_file = get_theme_file_uri('/assets/js/block-extensions/post-excerpt.js');
    
    // Generate a version number based on file modification time or use theme version
    $asset_version = file_exists($js_file_path) 
        ? filemtime($js_file_path) 
        : wp_get_theme()->get('Version');

    wp_enqueue_script(
        'moiraine-post-excerpt-extension',
        $asset_file,
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-compose', 'wp-hooks', 'wp-i18n'),
        $asset_version,
        true
    );
}
add_action('enqueue_block_editor_assets', 'moiraine_enqueue_block_extensions');

/**
 * Filter the post excerpt block output to add link functionality.
 */
function moiraine_filter_post_excerpt_block_output($block_content, $block) {
    if (!is_array($block) || $block['blockName'] !== 'core/post-excerpt') {
        return $block_content;
    }

    // Check if we have our custom attributes
    $attributes = $block['attrs'] ?? [];
    $link_to_post = $attributes['linkToPost'] ?? false;
    
    if (!$link_to_post) {
        return $block_content;
    }
    
    // Get the current post ID in the query
    $post_id = get_the_ID();
    if (!$post_id) {
        return $block_content;
    }
    
    $post_url = get_permalink($post_id);
    $has_underline = isset($attributes['underlineLink']) && $attributes['underlineLink'];
    $underline_class = $has_underline ? '' : ' no-underline-link';
    
    // Find the excerpt text and wrap it in a link
    // This is a simplified approach - you might need to adjust based on your block structure
    $excerpt_content = preg_replace(
        '/<p class="(.*?)">(.*?)<\/p>/',
        '<p class="$1"><a href="' . esc_url($post_url) . '" class="excerpt-link' . esc_attr($underline_class) . '">$2</a></p>',
        $block_content,
        1
    );
    
    return $excerpt_content ?: $block_content;
}
add_filter('render_block', 'moiraine_filter_post_excerpt_block_output', 10, 2);

/**
 * Add custom styles for excerpt links
 */
function moiraine_excerpt_link_styles() {
    ?>
    <style>
        .excerpt-link.no-underline-link {
            text-decoration: none;
        }
        
        /* Editor-specific styles */
        .editor-styles-wrapper .moiraine-linked-excerpt.no-underline {
            text-decoration: none;
        }
        .editor-styles-wrapper .moiraine-linked-excerpt {
            color: var(--wp--preset--color--primary);
        }
    </style>
    <?php
}
add_action('wp_head', 'moiraine_excerpt_link_styles');
add_action('admin_head', 'moiraine_excerpt_link_styles');
