<?php

/*
 * Plugin Name:       WP Algolia Search Addons
 * Description:       Addons for WP Search with Algolia
 * Version:           0.3.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Craftweeks
 * Author URI:        https://craftweeks.com
 * License:           MIT License
 * Text Domain:       wp-algolia-search-addons
 * Domain Path:       /languages
 * Requires Plugins:  wp-search-with-algolia
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

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

final class WP_Algolia_Search_Addons
{
    /**
     * The single instance of the class.
     *
     * @var WP_Algolia_Search_Addons
     */
    private static $_instance = null;

    /**
     * Main WP_Algolia_Search_Addons Instance.
     *
     * Ensures only one instance of WP_Algolia_Search_Addons is loaded or can be loaded.
     *
     * @static
     * @return WP_Algolia_Search_Addons - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * WP_Algolia_Search_Addons Constructor.
     */
    private function __construct()
    {
        $this->hooks();
    }

    /**
     * Hook into actions and filters.
     */
    private function hooks()
    {
        // Exclude specific pages from post indexing
        add_filter('algolia_should_index_post', [$this, 'custom_should_index_post'], 10, 2);
        add_filter('algolia_should_index_searchable_post', [$this, 'custom_should_index_post'], 10, 2);

        // Polylang integration
        add_filter('algolia_custom_template_location', [$this, 'load_autocomplete_template'], 11, 2);
        add_filter('algolia_post_shared_attributes', [$this, 'add_locales_to_records'], 10, 2);
        add_filter('algolia_searchable_post_shared_attributes', [$this, 'add_locales_to_records'], 10, 2);
        add_filter('algolia_searchable_posts_index_settings', [$this, 'add_locale_to_facets']);
        add_filter('algolia_posts_index_settings', [$this, 'add_locale_to_facets']);
        add_filter('algolia_terms_index_settings', [$this, 'add_locale_to_facets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_locale'], 99);

        // URL Rewriting for Algolia Search
        add_filter('algolia_post_shared_attributes', [$this, 'replace_algolia_post_shared_attributes_url'], 10, 2);
        add_filter('algolia_searchable_post_shared_attributes', [$this, 'replace_algolia_post_shared_attributes_url'], 10, 2);
        add_filter('algolia_term_record', [$this, 'replace_algolia_term_record_url'], 10, 2);
        add_filter('algolia_get_post_images', [$this, 'replace_algolia_post_images_url'], 10, 1);

        // Admin settings
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_plugin_menu']);

        // Override page template
        add_filter('template_include', [$this, 'override_page_template'], 99);
    }

    /**
     * Exclude Specific Pages From Post Indexing in Algolia.
     *
     * @param bool    $should_index
     * @param WP_Post $post
     * @return bool
     */
    public function custom_should_index_post($should_index, WP_Post $post)
    {
        if (false === $should_index) {
            return $should_index;
        }

        if ($post->post_type !== 'page') {
            return $should_index;
        }

        $posts_to_exclude = get_option('algolia_addons_excluded_posts', array());

        if (in_array($post->ID, $posts_to_exclude, TRUE)) {
            return false;
        }

        return $should_index;
    }

    /**
     * Check if Polylang is active.
     *
     * @return bool
     */
    public function is_polylang_active()
    {
        return defined('POLYLANG_VERSION');
    }

    /**
     * Check if Polylang integration should be used.
     *
     * @return bool
     */
    private function should_integrate_polylang()
    {
        return $this->is_polylang_active() && get_option('algolia_addons_enable_polylang', false);
    }

    /**
     * Load the autocomplete template.
     *
     * @param string $location
     * @param string $file
     * @return string
     */
    public function load_autocomplete_template($location, $file)
    {
        if ($this->should_integrate_polylang() && in_array($file, ['autocomplete.php', 'instantsearch.php'])) {
            return ALGOLIA_ADDONS_PATH_TEMPLATE_PATH . $file;
        }
        return $location;
    }

    /**
     * Add the locale of every post to every record.
     *
     * @param array   $attrs
     * @param WP_Post $post
     * @return array
     */
    public function add_locales_to_records(array $attrs, WP_Post $post)
    {
        if ($this->should_integrate_polylang() && function_exists('pll_get_post_language')) {
            $attrs['locale'] = pll_get_post_language($post->ID, "locale");
        }
        return $attrs;
    }

    /**
     * Register the locale attribute as an Algolia facet.
     *
     * @param array $settings
     * @return array
     */
    public function add_locale_to_facets(array $settings)
    {
        if ($this->should_integrate_polylang()) {
            $settings['attributesForFaceting'][] = 'locale';
        }
        return $settings;
    }

    /**
     * Expose the current locale of the displayed page in JavaScript.
     */
    public function enqueue_locale()
    {
        if ($this->should_integrate_polylang()) {
            $current_locale = sanitize_text_field(get_locale());
            wp_add_inline_script('algolia-search', sprintf('var current_locale = "%s";', $current_locale), 'before');
        }
    }

    /**
     * Get the deployment URL.
     *
     * @return string
     */
    private function get_deployment_url()
    {
        $url = get_option('algolia_addons_deployment_url');
        return !empty($url) ? $url : site_url();
    }

    /**
     * Replace the URL in Algolia post shared attributes.
     *
     * @param array   $shared_attributes
     * @param WP_Post $post
     * @return array
     */
    public function replace_algolia_post_shared_attributes_url($shared_attributes, $post)
    {
        $shared_attributes['permalink'] = str_replace(
            site_url(),
            $this->get_deployment_url(),
            $shared_attributes['permalink']
        );
        return $shared_attributes;
    }

    /**
     * Replace the URL in Algolia term record.
     *
     * @param array $record
     * @param mixed $item
     * @return array
     */
    public function replace_algolia_term_record_url($record, $item)
    {
        $record['permalink'] = str_replace(
            site_url(),
            $this->get_deployment_url(),
            $record['permalink']
        );
        return $record;
    }

    /**
     * Replace the URL in Algolia post images.
     *
     * @param array $images
     * @return array
     */
    public function replace_algolia_post_images_url(array $images)
    {
        $deployment_url = $this->get_deployment_url();

        return array_map(
            function ($image) use ($deployment_url) {
                return [
                    'url'    => str_replace(
                        site_url(),
                        $deployment_url,
                        $image['url']
                    ),
                    'width'  => $image['width'],
                    'height' => $image['height'],
                ];
            },
            $images
        );
    }

    /**
     * Register plugin settings.
     */
    public function register_settings()
    {
        register_setting('algolia_addons_settings', 'algolia_addons_excluded_posts');
        register_setting('algolia_addons_settings', 'algolia_addons_override_pages');
        register_setting('algolia_addons_settings', 'algolia_addons_enable_polylang');
        register_setting('algolia_addons_settings', 'algolia_addons_deployment_url');
    }

    /**
     * Add the plugin menu page.
     */
    public function add_plugin_menu()
    {
        add_options_page(
            esc_html__('Algolia Search Addons', 'wp-algolia-search-addons'),
            esc_html__('Algolia Search Addons', 'wp-algolia-search-addons'),
            'manage_options',
            'algolia-addons',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page()
    {
        include_once ALGOLIA_ADDONS_PATH . 'includes/admin/partials/page-settings.php';
    }

    /**
     * Override page template with Algolia instantsearch page.
     *
     * @param string $template
     * @return string
     */
    public function override_page_template($template)
    {
        if (!is_page()) {
            return $template;
        }

        $override_pages = get_option('algolia_addons_override_pages', array());
        if (!in_array(get_the_ID(), $override_pages)) {
            return $template;
        }

        $this->enqueue_instantsearch_assets();

        $algolia_settings = get_option('algolia_settings', array());
        $template_name = isset($algolia_settings['instantsearch_template']) && $algolia_settings['instantsearch_template'] === 'modern' ? 'instantsearch-modern.php' : 'instantsearch.php';

        return ALGOLIA_ADDONS_PATH_TEMPLATE_PATH . $template_name;
    }

    /**
     * Enqueue instantsearch assets.
     */
    private function enqueue_instantsearch_assets()
    {
        $plugin_data = $this->get_algolia_plugin_data();
        $version = isset($plugin_data['Version']) ? $plugin_data['Version'] : '2.8.1'; // Fallback version

        wp_enqueue_style('algolia-instantsearch', plugins_url('css/algolia-instantsearch.css', 'wp-search-with-algolia/wp-search-with-algolia.php'), [], $version);
        wp_enqueue_script('algolia-instantsearch', plugins_url('js/instantsearch.js/dist/instantsearch.production.min.js', 'wp-search-with-algolia/wp-search-with-algolia.php'), [], $version, true);
    }

    /**
     * Get WP Search with Algolia plugin data.
     *
     * @return array
     */
    private function get_algolia_plugin_data()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();
        foreach ($plugins as $plugin_file => $plugin_data) {
            if (strpos($plugin_file, 'wp-search-with-algolia.php') !== false) {
                return $plugin_data;
            }
        }

        return [];
    }
}

/**
 * Returns the main instance of WP_Algolia_Search_Addons.
 *
 * @return WP_Algolia_Search_Addons
 */
function wp_algolia_search_addons()
{
    return WP_Algolia_Search_Addons::instance();
}

// Get the plugin running.
wp_algolia_search_addons();
