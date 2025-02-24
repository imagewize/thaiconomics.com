<?php
/**
 * This file adds functions to the Moiraine WordPress theme.
 *
 * @package moiraine
 * @author  Mike McAlister
 * @license GNU General Public License v2 or later
 * @link    https://olliewp.com
 */

namespace Moiraine;

/**
 * Set up theme defaults and register various WordPress features.
 */
function setup() {

	// Enqueue editor styles and fonts.
	add_editor_style( 'style.css' );

	// Remove core block patterns.
	remove_theme_support( 'core-block-patterns' );

	// Add custom image size for index page.
	add_image_size('featured-vertical', 350, 525, true);
	
	// Add the size to WordPress size dropdown
	add_filter('image_size_names_choose', function($sizes) {
		return array_merge($sizes, [
			'featured-vertical' => __('Featured Vertical', 'moiraine')
		]);
	});
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\setup' );

// Add this new function after setup()
function add_image_size_to_blocks() {
    add_filter('block_editor_settings_all', function($settings) {
        $settings['imageSizes'][] = [
            'slug' => 'featured-vertical',
            'name' => __('Featured Vertical', 'moiraine')
        ];
        return $settings;
    });
}
add_action('init', __NAMESPACE__ . '\add_image_size_to_blocks');

/**
 * Enqueue styles.
 */
function enqueue_style_sheet() {
	wp_enqueue_style( sanitize_title( __NAMESPACE__ ), get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_style_sheet' );


/**
 * Add block style variations.
 */
function register_block_styles() {

	$block_styles = array(
		'core/list'         => array(
			'list-check'        => __( 'Check', 'moiraine' ),
			'list-check-circle' => __( 'Check Circle', 'moiraine' ),
			'list-boxed'        => __( 'Boxed', 'moiraine' ),
		),
		'core/code'         => array(
			'dark-code' => __( 'Dark', 'moiraine' ),
		),
		'core/cover'        => array(
			'blur-image-less' => __( 'Blur Image Less', 'moiraine' ),
			'blur-image-more' => __( 'Blur Image More', 'moiraine' ),
			'rounded-cover'   => __( 'Rounded', 'moiraine' ),
		),
		'core/column'       => array(
			'column-box-shadow' => __( 'Box Shadow', 'moiraine' ),
		),
		'core/post-excerpt' => array(
			'excerpt-truncate-2' => __( 'Truncate 2 Lines', 'moiraine' ),
			'excerpt-truncate-3' => __( 'Truncate 3 Lines', 'moiraine' ),
			'excerpt-truncate-4' => __( 'Truncate 4 Lines', 'moiraine' ),
		),
		'core/group'        => array(
			'column-box-shadow' => __( 'Box Shadow', 'moiraine' ),
			'background-blur'   => __( 'Background Blur', 'moiraine' ),
		),
		'core/separator'    => array(
			'separator-dotted' => __( 'Dotted', 'moiraine' ),
			'separator-thin'   => __( 'Thin', 'moiraine' ),
		),
		'core/image'        => array(
			'rounded-full' => __( 'Rounded Full', 'moiraine' ),
			'media-boxed'  => __( 'Boxed', 'moiraine' ),
		),
		'core/preformatted' => array(
			'preformatted-dark' => __( 'Dark Style', 'moiraine' ),
		),
		'core/post-terms'   => array(
			'term-button' => __( 'Button Style', 'moiraine' ),
		),
		'core/video'        => array(
			'media-boxed' => __( 'Boxed', 'moiraine' ),
		),
	);

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				array(
					'name'  => $style_name,
					'label' => $style_label,
				)
			);
		}
	}
}
add_action( 'init', __NAMESPACE__ . '\register_block_styles' );


/**
 * Load custom block styles only when the block is used.
 */
function enqueue_custom_block_styles() {

	// Scan our styles folder to locate block styles.
	$files = glob( get_template_directory() . '/assets/styles/*.css' );

	foreach ( $files as $file ) {

		// Get the filename and core block name.
		$filename   = basename( $file, '.css' );
		$block_name = str_replace( 'core-', 'core/', $filename );

		wp_enqueue_block_style(
			$block_name,
			array(
				'handle' => "moiraine-block-{$filename}",
				'src'    => get_theme_file_uri( "assets/styles/{$filename}.css" ),
				'path'   => get_theme_file_path( "assets/styles/{$filename}.css" ),
			)
		);
	}
}
add_action( 'init', __NAMESPACE__ . '\enqueue_custom_block_styles' );


/**
 * Register pattern categories.
 */
function pattern_categories() {

	$block_pattern_categories = array(
		'moiraine/card'           => array(
			'label' => __( 'Cards', 'moiraine' ),
		),
		'moiraine/call-to-action' => array(
			'label' => __( 'Call To Action', 'moiraine' ),
		),
		'moiraine/features'       => array(
			'label' => __( 'Features', 'moiraine' ),
		),
		'moiraine/hero'           => array(
			'label' => __( 'Hero', 'moiraine' ),
		),
		'moiraine/pages'          => array(
			'label' => __( 'Pages', 'moiraine' ),
		),
		'moiraine/posts'          => array(
			'label' => __( 'Posts', 'moiraine' ),
		),
		'moiraine/pricing'        => array(
			'label' => __( 'Pricing', 'moiraine' ),
		),
		'moiraine/testimonial'    => array(
			'label' => __( 'Testimonials', 'moiraine' ),
		),
	);

	foreach ( $block_pattern_categories as $name => $properties ) {
		register_block_pattern_category( $name, $properties );
	}
}
add_action( 'init', __NAMESPACE__ . '\pattern_categories', 9 );


/**
 * Remove last separator on blog/archive if no pagination exists.
 */
function is_paginated() {
	global $wp_query;
	if ( $wp_query->max_num_pages < 2 ) {
		echo '<style>.blog .wp-block-post-template .wp-block-post:last-child .entry-content + .wp-block-separator, .archive .wp-block-post-template .wp-block-post:last-child .entry-content + .wp-block-separator, .blog .wp-block-post-template .wp-block-post:last-child .entry-content + .wp-block-separator, .search .wp-block-post-template .wp-block-post:last-child .wp-block-post-excerpt + .wp-block-separator { display: none; }</style>';
	}
}
add_action( 'wp_head', __NAMESPACE__ . '\is_paginated' );


/**
 * Add a Sidebar template part area
 *
 * @param array $areas Array of template part areas.
 * @return array
 */
function template_part_areas( array $areas ) {
	$areas[] = array(
		'area'        => 'sidebar',
		'area_tag'    => 'section',
		'label'       => __( 'Sidebar', 'moiraine' ),
		'description' => __( 'The Sidebar template defines a page area that can be found on the Page (With Sidebar) template.', 'moiraine' ),
		'icon'        => 'sidebar',
	);

	return $areas;
}
add_filter( 'default_wp_template_part_areas', __NAMESPACE__ . '\template_part_areas' );
