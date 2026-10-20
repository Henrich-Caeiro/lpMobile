<?php
/**
 * Acione Mobile Theme Functions and Definitions
 *
 * @package AcioneMobile
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Setup theme defaults and registers support for various WordPress features.
 */
function acionemobile_theme_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails' );

    // Add support for full and wide align images.
    add_theme_support( 'align-wide' );

    // Add support for responsive embedded content.
    add_theme_support( 'responsive-embeds' );

    // Add support for Block Styles.
    add_theme_support( 'wp-block-styles' );

    // Add support for editor styles.
    add_theme_support( 'editor-styles' );
    add_editor_style( array(
        'assets/css/fonts.css',
        'assets/css/terminal-extracted.css',
        'assets/css/acione-theme.css',
    ) );
}
add_action( 'after_setup_theme', 'acionemobile_theme_setup' );

/**
 * Enqueue scripts and styles for frontend.
 */
function acionemobile_enqueue_assets() {
    $theme_version = wp_get_theme()->get( 'Version' );
    $theme_uri     = get_template_directory_uri();

    // 1. Typography / Fonts
    wp_enqueue_style(
        'acione-fonts',
        $theme_uri . '/assets/css/fonts.css',
        array(),
        $theme_version
    );

    // 2. Terminal Industries Extracted Styles
    wp_enqueue_style(
        'terminal-extracted',
        $theme_uri . '/assets/css/terminal-extracted.css',
        array( 'acione-fonts' ),
        $theme_version
    );

    $js_ver  = file_exists( get_template_directory() . '/assets/js/acione-interactive.js' ) ? filemtime( get_template_directory() . '/assets/js/acione-interactive.js' ) : $theme_version;
    $css_ver = file_exists( get_template_directory() . '/assets/css/acione-theme.css' ) ? filemtime( get_template_directory() . '/assets/css/acione-theme.css' ) : $theme_version;

    // 3. Acione Theme Custom & Refined Styles
    wp_enqueue_style(
        'acione-theme-custom',
        $theme_uri . '/assets/css/acione-theme.css',
        array( 'terminal-extracted' ),
        $css_ver
    );

    // 4. Main style.css
    wp_enqueue_style(
        'acione-main-style',
        get_stylesheet_uri(),
        array( 'acione-theme-custom' ),
        $theme_version
    );

    // 5. Interactive JS Engine
    wp_enqueue_script(
        'acione-interactive',
        $theme_uri . '/assets/js/acione-interactive.js',
        array(),
        $js_ver,
        true
    );

    // Pass dynamic variables to JS
    wp_add_inline_script(
        'acione-interactive',
        'window.AcioneThemeUri = ' . json_encode( $theme_uri ) . ';',
        'before'
    );
}
add_action( 'wp_enqueue_scripts', 'acionemobile_enqueue_assets' );

/**
 * Register Acione Mobile block pattern category.
 */
function acionemobile_register_pattern_category() {
    register_block_pattern_category(
        'acione-mobile',
        array( 'label' => __( 'Acione Mobile', 'acionemobile' ) )
    );
}
add_action( 'init', 'acionemobile_register_pattern_category' );
