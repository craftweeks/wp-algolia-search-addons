<?php

/*
 * Plugin Name:       WP Algolia Search Addons
 * Description:       Addons for WP Search with Algolia
 * Version:           0.2.0
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

// Function to check if Polylang is active
function is_polylang_active() {
    return defined('POLYLANG_VERSION');
}

// Function to check if Polylang integration should be used
function should_integrate_polylang() {
    return is_polylang_active() && get_option('algolia_addons_enable_polylang', false);
}

// Function to load the autocomplete template.
function load_autocomplete_template($location, $file)
{
    // Only load the custom template file if Polylang integration should be used and the file is either 'autocomplete.php' or 'instantsearch.php'.
    if (should_integrate_polylang() && in_array($file, ['autocomplete.php', 'instantsearch.php'])) {
        return ALGOLIA_ADDONS_PATH_TEMPLATE_PATH . $file;
    }
    return $location;
}
add_filter('algolia_custom_template_location', 'load_autocomplete_template', 11, 2);

// Add the locale of every post to every record of every post type indexed.
function add_locales_to_records(array $attrs, WP_Post $post)
{
    // Only add locale if Polylang integration should be used
    if (should_integrate_polylang() && function_exists('pll_get_post_language')) {
        $attrs['locale'] = pll_get_post_language($post->ID, "locale");
    }
    return $attrs;
}
add_filter('algolia_post_shared_attributes', 'add_locales_to_records', 10, 2);
add_filter('algolia_searchable_post_shared_attributes', 'add_locales_to_records', 10, 2);

// Register the locale attribute as an Algolia facet which will allow us to filter on the currently displayed locale.
function add_locale_to_facets(array $settings)
{
    // Only add locale facet if Polylang integration should be used
    if (should_integrate_polylang()) {
        $settings['attributesForFaceting'][] = 'locale';
    }
    return $settings;
}
add_filter('algolia_searchable_posts_index_settings', 'add_locale_to_facets');
add_filter('algolia_posts_index_settings', 'add_locale_to_facets');
add_filter('algolia_terms_index_settings', 'add_locale_to_facets');

// Expose the current locale of the displayed page in JavaScript.
function enqueue_locale() {
    // Only expose locale if Polylang integration should be used
    if (should_integrate_polylang()) {
        $current_locale = sanitize_text_field(get_locale());
        wp_add_inline_script('algolia-search', sprintf('var current_locale = "%s";', $current_locale), 'before');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_locale', 99);

// URL Rewriting for Algolia Search
function deployment_url() {
    $url = get_option('algolia_addons_deployment_url');
    return !empty($url) ? $url : site_url(); // Fallback to site_url if not set
}

function replace_algolia_post_shared_attributes_url($shared_attributes, $post) {
    $shared_attributes['permalink'] = str_replace(
        site_url(),
        deployment_url(),
        $shared_attributes['permalink']
    );
    return $shared_attributes;
}
add_filter('algolia_post_shared_attributes', 'replace_algolia_post_shared_attributes_url', 10, 2);
add_filter('algolia_searchable_post_shared_attributes', 'replace_algolia_post_shared_attributes_url', 10, 2);

function replace_algolia_term_record_url($record, $item) {
    $record['permalink'] = str_replace(
        site_url(),
        deployment_url(),
        $record['permalink']
    );
    return $record;
}
add_filter('algolia_term_record', 'replace_algolia_term_record_url', 10, 2);

function replace_algolia_post_images_url(array $images) {
    return array_map(
        function($image) {
            return array(
                'url'    => str_replace(
                    site_url(),
                    deployment_url(),
                    $image['url']
                ),
                'width'  => $image['width'],
                'height' => $image['height'],
            );
        },
        $images
    );
}
add_filter('algolia_get_post_images', 'replace_algolia_post_images_url', 10, 1);

// Register plugin settings
function algolia_addons_register_settings() {
    register_setting('algolia_addons_settings', 'algolia_addons_excluded_posts');
    register_setting('algolia_addons_settings', 'algolia_addons_enable_polylang');
    register_setting('algolia_addons_settings', 'algolia_addons_deployment_url');
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
