# Debugging Guide

This guide provides steps to troubleshoot common issues with the WP Algolia Search Addons plugin.

## General Troubleshooting Steps

Before diving into specific issues, here are some general steps that can help identify the problem:

1.  **Enable WordPress Debugging:** Add the following lines to your `wp-config.php` file to see if there are any PHP errors.
    ```php
    define( 'WP_DEBUG', true );
    define( 'WP_DEBUG_LOG', true );
    define( 'WP_DEBUG_DISPLAY', false );
    ```
    Errors will be logged to `/wp-content/debug.log`.

2.  **Check Browser Console:** Open your browser's developer tools (usually by pressing F12) and check the "Console" tab for any JavaScript errors on your site's frontend and on the plugin settings page.

3.  **Plugin/Theme Conflict:** Temporarily deactivate other plugins and switch to a default WordPress theme (like Twenty Twenty-Four) to see if the issue persists. This helps rule out conflicts.

4.  **Check Algolia Credentials:** Ensure your Algolia Application ID and API Keys are correctly configured in the main **WP Search with Algolia** plugin settings.

## Common Issues

### Search results are not updating or pages are not being excluded

After changing settings in this addon, you must re-index your content.

1.  Go to **Settings > Algolia Search Addons** and verify your settings are saved correctly.
2.  Go to **Algolia Search > Indexing** in your WordPress admin panel.
3.  Click the **Re-index all...** button for the indices you want to update (e.g., "Re-index all posts").
4.  Log in to your [Algolia Dashboard](https://www.algolia.com/dashboard) and check the relevant index to confirm that the records have been updated or the excluded pages are no longer present.

### Polylang integration is not working

This is an optional feature. To use it, you must have the Polylang plugin installed and activated.

1.  **Check Activation:** Ensure the Polylang plugin is installed and activated.
2.  **Enable Integration:** Go to **Settings > Algolia Search Addons** and make sure the "Polylang Integration" switch is turned on. If the switch is disabled, it means the Polylang plugin is not active.
3.  **Re-index:** Re-index your content after enabling the integration.
4.  **Verify Algolia Records:** In your Algolia dashboard, check your indexed records. They should contain a `locale` attribute (e.g., `en_US`). If not, the integration is not working correctly.
5.  **Verify Facets:** In your Algolia index configuration, under "Faceting", make sure `locale` is added as an "Attribute for faceting".
6.  **Check Frontend JavaScript:** On your website, open the browser console and type `current_locale`. It should return the current language's locale string (e.g., `"en_US"`). If it returns `undefined` or is incorrect, there might be a script issue.

### WP-to-Static URL rewriting is not working

1.  **Check URL:** Go to **Settings > Algolia Search Addons** and ensure the "Deployment URL" is correctly entered and saved.
2.  **Re-index:** You must re-index your content after setting or changing the deployment URL.
3.  **Verify Algolia Records:** In your Algolia dashboard, inspect the `permalink` and image `url` attributes of your records. They should be updated with the new deployment URL.
