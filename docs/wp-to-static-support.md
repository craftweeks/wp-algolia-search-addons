# WP-to-Static Support

This document provides a detailed guide on how to use the WP-to-Static support feature in the WP Algolia Search Addons plugin.

## Overview

The WP-to-Static support feature allows you to rewrite the URLs in the Algolia search results to match the URLs of your static site.

This is useful if you are using a static site generator like WP2Static to create a static version of your WordPress site.

## Configuration

To enable the WP-to-Static support, you need to enter the deployment URL of your static site in the **Settings > Algolia Search Addons** page.

The deployment URL is the URL of your static site, for example, `https://www.example.com`.

## Usage

Once the deployment URL is configured, the plugin will automatically rewrite the URLs in the Algolia search results to match the deployment URL.

For example, if your WordPress site is running on `http://localhost/wordpress` and your deployment URL is `https://www.example.com`, the plugin will rewrite the URLs in the search results from `http://localhost/wordpress/my-post` to `https://www.example.com/my-post`.
