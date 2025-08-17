# Changelog

## [0.3.0] - 2025-08-17

### Added
- **Override Search Pages with Instantsearch**
  - New admin interface to select pages to be replaced with the Algolia instantsearch page.
  - Implemented `template_include` filter to override the page template.
  - Enqueue instantsearch assets from the main WP Search with Algolia plugin.
- **New Debugging Guide:** A new debugging guide has been added to the documentation to help users troubleshoot common issues.

### Changed
- **Refactored Plugin Structure:** The plugin has been refactored to use a more modern, object-oriented approach, improving maintainability and extensibility.
- **Optional Polylang Integration:** The Polylang integration is now optional and can be enabled or disabled in the settings.
- **Improved Admin UI/UX:** The admin settings page has been updated for a better user experience.
- **Updated Documentation:** All documentation has been updated to reflect the new features and changes.

## [0.2.0] - 2025-02-28

### Added
- **Exclude Pages from Indexing**  
  - New admin interface to select pages excluded from Algolia search indexing.  
  - Implemented `custom_should_index_post` filter to respect exclusion rules.  

- **WP-to-Static URL Rewriting**  
  - Added "Deployment URL" field in settings to rewrite search result URLs for static sites.  
  - Modified permalinks and image URLs in Algolia records to use the configured deployment URL.  

- **Admin Settings Page**  
  - Added a dedicated settings page under **Settings > Algolia Search Addons**.  
  - UI components for managing excluded pages, Polylang integration toggle, and deployment URL.

### Changed
- **Polylang Integration**  
  - Added conditional checks to enable/disable integration via settings (`should_integrate_polylang`).  
  - Refined locale attribute handling to only apply when Polylang is active and configured.  

- **Code Refactoring**  
  - Registered plugin settings (`algolia_addons_excluded_posts`, `algolia_addons_enable_polylang`, `algolia_addons_deployment_url`).  
  - Improved template loading logic to conditionally override Algolia templates only when needed.

### Fixed

- **Locale Filter in Instant Search**
  - Added a locale filter (`filters: 'locale:"' + current_locale + '"`) to Algolia's Instant Search configuration to ensure results are scoped to the active language.

---

**Note**: This release focuses on configurability and integration stability. Full WP-to-Static support remains in progress.

## [0.1.0] - 2024-07-24

### Added
- **Polylang Integration**  
  - Locale Attributes: Added support for filtering search results using Polylang locale attributes.  
  - Language-Specific Search: Implemented search results and autocomplete suggestions tailored to the user's active language.  
  - Faceted Search: Enabled locale-based faceted search for refined result filtering.  
  - Admin Settings Page: Added a basic configuration page in the WordPress admin dashboard.  

- **Infrastructure**  
  - Added `.gitignore` file for better repository management.  
  - Included `LICENSE` file (MIT License).  
  - Created `README.md` with documentation, installation instructions, and contribution guidelines.  

- **Code Structure**  
  - Added templates (`autocomplete.php`, `instantsearch.php`) for search UI components.  
  - Implemented PHP hooks and filters for Algolia integration (e.g., `add_locales_to_records`, `add_locale_to_facets`).  

### Internal
- Initial release of the plugin as an experimental extension for WP Search with Algolia.
