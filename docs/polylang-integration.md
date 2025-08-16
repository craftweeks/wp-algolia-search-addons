# Polylang Integration

This document provides a detailed guide on how to use the Polylang integration feature in the WP Algolia Search Addons plugin.

## Overview

The Polylang integration feature allows you to create a seamless multilingual search experience for your users.

When this feature is enabled, the plugin will automatically:
- Add the locale of every post to the Algolia records.
- Add the locale attribute as an Algolia facet, which allows you to filter search results by language.
- Expose the current locale of the displayed page in JavaScript, which can be used to create a language-aware search UI.

## Configuration

To use the Polylang integration, the Polylang plugin must be installed and activated on your WordPress site.

Once Polylang is active, you can enable the integration by going to the **Settings > Algolia Search Addons** page and toggling the "Enable Polylang Integration" switch. If Polylang is not active, this option will be disabled.

## Usage

Once the integration is enabled, you can use the `current_locale` JavaScript variable to create a language-aware search UI.

For example, you can use the following code to filter the search results by the current language in your Algolia instantsearch configuration:

```javascript
search.addWidgets([
  instantsearch.widgets.configure({
    filters: 'locale:"' + current_locale + '"'
  })
]);
```
