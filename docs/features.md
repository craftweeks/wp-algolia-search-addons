# Features

This document provides a detailed overview of the features available in the WP Algolia Search Addons plugin.

## Exclude Pages from Indexing

This feature allows you to exclude specific pages from being indexed by Algolia. This is useful for pages that you don't want to appear in search results, such as privacy policy pages, terms of service pages, or other administrative pages.

The settings page provides an intuitive user interface to manage the excluded pages. You can easily search for pages and move them between the "Available Pages" and "Excluded Pages" lists.

## Polylang Integration

This feature integrates the plugin with the Polylang plugin to provide a seamless multilingual search experience.

When this feature is enabled, the plugin will:
- Add the locale of every post to the Algolia records.
- Add the locale attribute as an Algolia facet, which allows you to filter search results by language.
- Expose the current locale of the displayed page in JavaScript, which can be used to create a language-aware search UI.

For more details, see the [Polylang Integration](polylang-integration.md) documentation.

## WP-to-Static Support

This feature allows you to rewrite the URLs in the Algolia search results to match the URLs of your static site.

This is useful if you are using a static site generator like WP2Static to create a static version of your WordPress site.

For more details, see the [WP-to-Static Support](wp-to-static-support.md) documentation.
