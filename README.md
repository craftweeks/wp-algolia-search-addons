# WP Algolia Search Addons

**WP Algolia Search Addons** is an experimental extension for the [WP Search with Algolia] plugin, available on WordPress.org. This add-on seamlessly connects Algolia with popular plugins like [Polylang] and [WP2Static], enhancing search functionality and improving user experience.

[WP Search with Algolia]: https://wordpress.org/plugins/wp-search-with-algolia/
[Polylang]: https://wordpress.org/plugins/polylang/
[WP2Static]: https://wp2static.com/

## Table of Contents
1. [Features](#features)
2. [Prerequisites](#prerequisites)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Screenshots](#screenshots)
6. [FAQ](#faq)
7. [Contributing](#contributing)
8. [Changelog](#changelog)
9. [License](#license)
10. [Acknowledgements](#acknowledgements)

## Features

- **Exclude Pages from Indexing**: Allows exclusion of specific pages from being indexed by Algolia, offering greater control over search results. The intuitive UI allows you to easily add or remove pages from the exclusion list.
- **Polylang Integration**: Integrates with Polylang to deliver locale-specific search results and suggestions, ensuring a tailored user experience for multilingual websites.
  - **Locale Attributes**: Integrates Polylang locale attributes for locale-based search filtering.
  - **Language-Specific Search**: Delivers search results relevant to the user's selected language.
  - **Faceted Search by Locale**: Enables locale-based faceted search for refined results.
  - **Autocomplete with Locale**: Provides locale-filtered autocomplete suggestions for multilingual users.
- **WP-to-Static Support**:
  - **URL Rewrite**: Rewrites site URLs with deployment URLs for static site generation.

## Prerequisites

- WordPress 5.0 or higher
- [WP Search with Algolia] plugin
- [Polylang] plugin (for multilingual support)

## Installation

1. **Download the Plugin**: Clone or download the repository to your local machine.
   ```bash
   git clone https://github.com/craftweeks/wp-algolia-search-addons.git
   ```
2. **Upload to WordPress**: Upload the plugin folder to the `/wp-content/plugins/` directory.
3. **Activate the Plugin**: Activate the plugin through the 'Plugins' menu in WordPress.
4. **Configure Algolia**: Ensure you have the [WP Search with Algolia] plugin installed and configured. Index your searchable posts.
5. **Configure Polylang**: Ensure you have the Polylang plugin installed and configured for multilingual support.

## Configuration

Once the plugin is installed and activated, navigate to the settings page by selecting **Settings** > **Algolia Search Addons** from the left-hand menu in the WordPress Admin dashboard. Here, you can configure the plugin to suit your specific needs.

The settings page allows you to:
- **Exclude Pages**: Select pages to exclude from Algolia search indexing. You can search for pages and move them between the "Available" and "Excluded" lists.
- **Enable/Disable Polylang Integration**: Toggle the Polylang integration on or off.
- **Set Deployment URL**: Configure a deployment URL for WP-to-Static support.

For more detailed documentation, please refer to our [documentation page](docs/index.md).

## Screenshots

![Settings page](/docs/screeshots/settings.jpg)

## FAQ

**Q: Does this plugin support other multilingual plugins?**
A: Currently, it supports Polylang. Support for other plugins may be added in the future.

## Contributing

We are thrilled to have you consider contributing to the WP Algolia Search Addons plugin! Your involvement is key to the success and improvement of this project. Here are some ways you can get involved:

- **Reporting Bugs**: Encountered a bug or an issue? Please let us know by [opening an issue] on our GitHub repository. Your feedback helps us make the plugin better for everyone.
- **Submitting Pull Requests**: Have a fix or a new feature in mind? We welcome your [Pull Requests] with open arms. Your contributions are highly valued and appreciated.
- **Sponsoring the Project**: Love what we're doing? You can support the ongoing development by [sponsoring us on Ko-fi]. Your generosity helps us dedicate more time and resources to the project.

Thank you for your interest and support. Together, we can make WP Algolia Search Addons even better!

[opening an issue]: https://github.com/craftweeks/wp-algolia-search-addons/issues
[Pull Requests]: https://github.com/craftweeks/wp-algolia-search-addons/pulls
[sponsoring us on Ko-fi]: https://ko-fi.com/your-kofi-page

## Changelog

For detailed release notes, see the [CHANGELOG](CHANGELOG.md) file.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Acknowledgements

- Thanks to the [WP Search with Algolia] and [Polylang] teams for their amazing plugins.
- Special thanks to all contributors and users who have provided feedback and suggestions.
