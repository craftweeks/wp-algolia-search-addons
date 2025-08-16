# Features

This document provides a detailed overview of the features available in the WP Algolia Search Addons plugin.

## Exclude Pages from Indexing

This feature allows you to exclude specific pages from being indexed by Algolia. This is useful for pages that you don't want to appear in search results, such as privacy policy pages, terms of service pages, or other administrative pages.

The settings page provides an intuitive user interface to manage the excluded pages. You can easily search for pages and move them between the "Available Pages" and "Excluded Pages" lists.

## Override Pages with Instantsearch

This feature allows you to override specific pages with the Algolia instantsearch page. This is useful for creating a dedicated search page on your site.

The settings page provides an intuitive user interface to manage the overridden pages. You can easily search for pages and move them between the "Available Pages" and "Overridden Pages" lists.

For more details, see the [Override Pages with Instantsearch](override-search-pages-with-instantsearch.md) documentation.

## Polylang Integration

This feature provides optional integration with the Polylang plugin for a seamless multilingual search experience. If Polylang is active, you can enable this feature to link search to specific languages.

When this feature is enabled, the plugin will:
- Add the locale of every post to the Algolia records.
- Add the locale attribute as an Algolia facet, which allows you to filter search results by language.
- Expose the current locale of the displayed page in JavaScript, which can be used to create a language-aware search UI.

For more details, see the [Polylang Integration](polylang-integration.md) documentation.

## WP-to-Static Support

This feature allows you to rewrite the URLs in the Algolia search results to match the URLs of your static site.

This is useful if you are using a static site generator like WP2Static to create a static version of your WordPress site.

For more details, see the [WP-to-Static Support](wp-to-static-support.md) documentation.
