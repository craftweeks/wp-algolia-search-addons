<?php

/*
 * Plugin Name:       WP Algolia Search Addons
 * Description:       Addons for WP Search with Algolia
 * Version:           0.1.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Craftweeks
 * Author URI:        https://craftweeks.com
 * License:           MIT License
 * Text Domain:       wp-algolia-search-addons
 * Domain Path:       /languages
 * Requires Plugins:  polylang, wp-search-with-algolia
 */

// Define the path to the Algolia addons directory if not already defined.
if (!defined('ALGOLIA_ADDONS_PATH')) {
    define(
        'ALGOLIA_ADDONS_PATH',
        __DIR__ . '/'
    );
}

// Define the path to the Algolia addons templates directory if not already defined.
if (!defined('ALGOLIA_ADDONS_PATH_TEMPLATE_PATH')) {
    define(
        'ALGOLIA_ADDONS_PATH_TEMPLATE_PATH',
        ALGOLIA_ADDONS_PATH . 'templates/'
    );
}

// Exclude Specific Pages From Post Indexing in Algolia
function custom_should_index_post( $should_index, WP_Post $post ) {
    if ( false === $should_index ) {
        return $should_index;
    }

    if ( $post->post_type !== 'page' ) {
        return $should_index;
    }

    // Get excluded posts from settings
    $posts_to_exclude = get_option('algolia_addons_excluded_posts', array());

    if (in_array($post->ID, $posts_to_exclude, TRUE)) {
        return false;
    }

    return $should_index;
}
// Alter both the posts index and the searchable_posts index for Algolia. 
add_filter('algolia_should_index_post', 'custom_should_index_post', 10, 2);
add_filter('algolia_should_index_searchable_post', 'custom_should_index_post', 10, 2);

// Function to load the autocomplete template.
function load_autocomplete_template($location, $file)
{
    // Check if the file is either 'autocomplete.php' or 'instantsearch.php'.
    if (!in_array($file, ['autocomplete.php', 'instantsearch.php'])) {
        return $location;
    }

    // Return the path to the template file.
    return ALGOLIA_ADDONS_PATH_TEMPLATE_PATH . $file;
}
add_filter('algolia_custom_template_location', 'load_autocomplete_template', 11, 2);

// Add the locale of every post to every record of every post type indexed.
function add_locales_to_records(array $attrs, WP_Post $post)
{
    // Check if the Polylang function exists and add the locale to the attributes.
    if (function_exists('pll_get_post_language')) {
        $attrs['locale'] = pll_get_post_language($post->ID, "locale");
    }
    return $attrs;
}

add_filter('algolia_post_shared_attributes', 'add_locales_to_records', 10, 2);
add_filter('algolia_searchable_post_shared_attributes', 'add_locales_to_records', 10, 2);

// Register the locale attribute as an Algolia facet which will allow us to filter on the currently displayed locale.
function add_locale_to_facets(array $settings)
{
    // Add 'locale' to the attributes for faceting.
    $settings['attributesForFaceting'][] = 'locale';

    return $settings;
}
add_filter('algolia_searchable_posts_index_settings', 'add_locale_to_facets');
add_filter('algolia_posts_index_settings', 'add_locale_to_facets');
add_filter('algolia_terms_index_settings', 'add_locale_to_facets');

// Expose the current locale of the displayed page in JavaScript.
function enqueue_locale() {
    // Get and sanitize the current locale.
    $current_locale = sanitize_text_field( get_locale() ); 
    // Add the current locale as an inline script before the 'algolia-search' script.
    wp_add_inline_script( 'algolia-search', sprintf('var current_locale = "%s";', $current_locale), 'before' );
}
add_action('wp_enqueue_scripts', 'enqueue_locale', 99);

// Register plugin settings
function algolia_addons_register_settings() {
    register_setting('algolia_addons_settings', 'algolia_addons_excluded_posts');
}
add_action('admin_init', 'algolia_addons_register_settings');

// Include the admin page
function add_plugin_menu() {

	add_options_page(
		esc_html__( 'Algolia Search Addons', 'wp-algolia-search-addons' ),
		esc_html__( 'Algolia Search Addons', 'wp-algolia-search-addons' ),
		'manage_options',
		'algolia-addons',
        'algolia_addons_settings_page' // Function to display the page content
        // The position in the menu order
    );
}

add_action('admin_menu', 'add_plugin_menu');

function algolia_addons_settings_page() {
    include_once plugin_dir_path( __FILE__ ) . 'includes/admin/partials/page-settings.php';
}
