<?php
if (!current_user_can('manage_options')) {
    return;
}

// Save settings if form was submitted
if (isset($_POST['algolia_addons_save_settings'])) {
    check_admin_referer('algolia_addons_settings');
    
    $excluded_posts = isset($_POST['excluded_posts']) ? array_map('intval', $_POST['excluded_posts']) : array();
    update_option('algolia_addons_excluded_posts', $excluded_posts);

    $override_pages = isset($_POST['override_pages']) ? array_map('intval', $_POST['override_pages']) : array();
    update_option('algolia_addons_override_pages', $override_pages);
    
    $enable_polylang = isset($_POST['enable_polylang']) ? true : false;
    update_option('algolia_addons_enable_polylang', $enable_polylang);
    
    $deployment_url = isset($_POST['deployment_url']) ? esc_url_raw($_POST['deployment_url']) : '';
    update_option('algolia_addons_deployment_url', $deployment_url);
    
    echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully!</p></div>';
}

// Get current settings
$excluded_posts = get_option('algolia_addons_excluded_posts', array());
$override_pages = get_option('algolia_addons_override_pages', array());
$enable_polylang = get_option('algolia_addons_enable_polylang', false);
$deployment_url = get_option('algolia_addons_deployment_url', '');

// Get all published pages
$pages = get_posts(array(
    'post_type' => 'page',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

// Enqueue required scripts and styles
wp_enqueue_style('wp-jquery-ui-dialog');
wp_enqueue_script('jquery-ui-dialog');
?>

<style>
.multiselect-container {
    display: flex;
    gap: 20px;
    align-items: stretch;
    margin: 0 0 20px 0;
    max-width: 800px;
}
.multiselect-box {
    flex: 1;
    min-width: 200px;
    display: flex;
    flex-direction: column;
}
.multiselect-box input[type="text"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.multiselect-box select {
    width: 100%;
    resize: vertical;
    min-height: 250px; /* Height for approximately 10 items */
    max-height: 600px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fff;
    flex-grow: 1;
}
.multiselect-box select option {
    padding: 4px 8px;
}
.multiselect-box select option:hover {
    background-color: #f0f0f0;
}
.multiselect-controls {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
    padding: 10px;
}
.multiselect-controls button {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s ease;
}
.multiselect-controls button:hover {
    background: #f0f0f0;
    border-color: #999;
}
.multiselect-controls button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 26px;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 26px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: #2196F3;
}
input:disabled + .slider {
    background-color: #ddd;
    cursor: not-allowed;
}
input:checked + .slider:before {
    transform: translateX(26px);
}
</style>

<div class="wrap">
    <h1>WP Algolia Search Addons</h1>

    <p>The "WP Algolia Search Addons" enhances the search functionality of WordPress websites using Algolia.</p>

    <hr/>

    <form method="post" action="">
        <?php wp_nonce_field('algolia_addons_settings'); ?>

        <h2 class="title">Exclude Pages from Indexing</h2>
        <p class="description">Select pages that should be excluded from Algolia search indexing.</p>
        
        <div class="multiselect-container">
            <div class="multiselect-box">
                <h4>Available Pages</h4>
                <input type="text" id="available-pages-search" placeholder="Search available pages...">
                <select id="available-pages" multiple>
                    <?php foreach ($pages as $page): 
                        if (!in_array($page->ID, $excluded_posts)): ?>
                            <option value="<?php echo esc_attr($page->ID); ?>">
                                <?php echo esc_html($page->post_title); ?> (ID: <?php echo esc_html($page->ID); ?>)
                            </option>
                        <?php endif;
                    endforeach; ?>
                </select>
            </div>
            
            <div class="multiselect-controls">
                <button type="button" id="add-pages" aria-label="Move selected pages to excluded list">›</button>
                <button type="button" id="remove-pages" aria-label="Move selected pages to available list">‹</button>
            </div>
            
            <div class="multiselect-box">
                <h4>Excluded Pages</h4>
                <input type="text" id="excluded-pages-search" placeholder="Search excluded pages...">
                <select id="excluded-pages" name="excluded_posts[]" multiple>
                    <?php foreach ($pages as $page): 
                        if (in_array($page->ID, $excluded_posts)): ?>
                            <option value="<?php echo esc_attr($page->ID); ?>">
                                <?php echo esc_html($page->post_title); ?> (ID: <?php echo esc_html($page->ID); ?>)
                            </option>
                        <?php endif;
                    endforeach; ?>
                </select>
            </div>
        </div>

        <hr/>

        <h2 class="title">Override Pages</h2>
        <p class="description">Select pages to override with the Algolia instant search page.</p>
        
        <div class="multiselect-container">
            <div class="multiselect-box">
                <h4>Available Pages</h4>
                <input type="text" id="available-override-pages-search" placeholder="Search available pages...">
                <select id="available-override-pages" multiple>
                    <?php foreach ($pages as $page): 
                        if (!in_array($page->ID, $override_pages)): ?>
                            <option value="<?php echo esc_attr($page->ID); ?>">
                                <?php echo esc_html($page->post_title); ?> (ID: <?php echo esc_html($page->ID); ?>)
                            </option>
                        <?php endif;
                    endforeach; ?>
                </select>
            </div>
            
            <div class="multiselect-controls">
                <button type="button" id="add-override-pages" aria-label="Move selected pages to override list">›</button>
                <button type="button" id="remove-override-pages" aria-label="Move selected pages to available list">‹</button>
            </div>
            
            <div class="multiselect-box">
                <h4>Overridden Search Pages</h4>
                <input type="text" id="override-pages-search" placeholder="Search overridden pages...">
                <select id="override-pages" name="override_pages[]" multiple>
                    <?php foreach ($pages as $page): 
                        if (in_array($page->ID, $override_pages)): ?>
                            <option value="<?php echo esc_attr($page->ID); ?>">
                                <?php echo esc_html($page->post_title); ?> (ID: <?php echo esc_html($page->ID); ?>)
                            </option>
                        <?php endif;
                    endforeach; ?>
                </select>
            </div>
        </div>

        <hr/>

        <h2 class="title">Polylang Integration</h2>
        <div class="polylang-settings">
            <label class="switch">
                <input type="checkbox" name="enable_polylang" 
                    <?php echo $enable_polylang ? 'checked' : ''; ?> 
                    <?php echo !$this->is_polylang_active() ? 'disabled' : ''; ?>>
                <span class="slider"></span>
            </label>
            <p class="description">
                <?php
                if (!$this->is_polylang_active()) {
                    echo 'Polylang is not installed or activated. Please install and activate Polylang to enable this feature.';
                } else {
                    echo 'Enable Polylang integration for multilingual search support. This feature enhances Algolia search by making it language-aware, ensuring that users receive search results in their selected language. For more details on configuring languages, please visit the <a href="' . admin_url('admin.php?page=mlang') . '">Polylang settings</a>.';
                }
                ?>
                <ol>
                    <li><strong>Locale Attributes:</strong> Integrates locale attributes from Polylang for locale-based filtering.</li>
                    <li><strong>Language-Specific Search:</strong> Ensures results are relevant to the user's selected language.</li>
                    <li><strong>Faceted Search by Locale:</strong> Enables search result refinement by language.</li>
                    <li><strong>Autocomplete with Locale:</strong> Provides locale-filtered autocomplete suggestions.</li>
                </ol>
            </p>
        </div>

        <hr/>

        <h2 class="title">WP-to-Static Support</h2>
        <label for="deployment_url"><strong>Deployment URL:</strong></label>
        <input type="url" name="deployment_url" value="<?php echo esc_attr($deployment_url); ?>" style="width: 100%; max-width: 400px;">
        <p class="description">This URL will be used to rewrite URLs in search results for static site deployment.  Leave empty to use the default WordPress site URL.</p>

        <p class="submit">
            <input type="submit" name="algolia_addons_save_settings" class="button-primary" value="Save Changes" />
        </p>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Exclude Pages
    const availableSelect = document.getElementById('available-pages');
    const excludedSelect = document.getElementById('excluded-pages');
    const addButton = document.getElementById('add-pages');
    const removeButton = document.getElementById('remove-pages');
    const availableSearch = document.getElementById('available-pages-search');
    const excludedSearch = document.getElementById('excluded-pages-search');

    function moveSelectedOptions(fromSelect, toSelect) {
        const selectedOptions = Array.from(fromSelect.selectedOptions);
        selectedOptions.forEach(option => toSelect.appendChild(option));
        updateButtonStates();
    }

    function updateButtonStates() {
        if (addButton) addButton.disabled = availableSelect.selectedOptions.length === 0;
        if (removeButton) removeButton.disabled = excludedSelect.selectedOptions.length === 0;
    }

    function filterOptions(select, searchTerm) {
        Array.from(select.options).forEach(option => {
            option.style.display = option.textContent.toLowerCase().includes(searchTerm.toLowerCase()) ? '' : 'none';
        });
    }

    if (availableSelect) {
        addButton.addEventListener('click', () => moveSelectedOptions(availableSelect, excludedSelect));
        removeButton.addEventListener('click', () => moveSelectedOptions(excludedSelect, availableSelect));
        availableSelect.addEventListener('dblclick', () => moveSelectedOptions(availableSelect, excludedSelect));
        excludedSelect.addEventListener('dblclick', () => moveSelectedOptions(excludedSelect, availableSelect));
        availableSelect.addEventListener('change', updateButtonStates);
        excludedSelect.addEventListener('change', updateButtonStates);
        availableSearch.addEventListener('input', () => filterOptions(availableSelect, availableSearch.value));
        excludedSearch.addEventListener('input', () => filterOptions(excludedSelect, excludedSearch.value));
        updateButtonStates();
    }

    // Override Pages
    const availableOverrideSelect = document.getElementById('available-override-pages');
    const overrideSelect = document.getElementById('override-pages');
    const addOverrideButton = document.getElementById('add-override-pages');
    const removeOverrideButton = document.getElementById('remove-override-pages');
    const availableOverrideSearch = document.getElementById('available-override-pages-search');
    const overrideSearch = document.getElementById('override-pages-search');

    function moveOverrideSelectedOptions(fromSelect, toSelect) {
        const selectedOptions = Array.from(fromSelect.selectedOptions);
        selectedOptions.forEach(option => toSelect.appendChild(option));
        updateOverrideButtonStates();
    }

    function updateOverrideButtonStates() {
        if (addOverrideButton) addOverrideButton.disabled = availableOverrideSelect.selectedOptions.length === 0;
        if (removeOverrideButton) removeOverrideButton.disabled = overrideSelect.selectedOptions.length === 0;
    }

    if (availableOverrideSelect) {
        addOverrideButton.addEventListener('click', () => moveOverrideSelectedOptions(availableOverrideSelect, overrideSelect));
        removeOverrideButton.addEventListener('click', () => moveOverrideSelectedOptions(overrideSelect, availableOverrideSelect));
        availableOverrideSelect.addEventListener('dblclick', () => moveOverrideSelectedOptions(availableOverrideSelect, overrideSelect));
        overrideSelect.addEventListener('dblclick', () => moveOverrideSelectedOptions(overrideSelect, availableOverrideSelect));
        availableOverrideSelect.addEventListener('change', updateOverrideButtonStates);
        overrideSelect.addEventListener('change', updateOverrideButtonStates);
        availableOverrideSearch.addEventListener('input', () => filterOptions(availableOverrideSelect, availableOverrideSearch.value));
        overrideSearch.addEventListener('input', () => filterOptions(overrideSelect, overrideSearch.value));
        updateOverrideButtonStates();
    }

    // Form submission handler
    document.querySelector('form').addEventListener('submit', function() {
        if (excludedSelect) {
            Array.from(excludedSelect.options).forEach(option => option.selected = true);
        }
        if (overrideSelect) {
            Array.from(overrideSelect.options).forEach(option => option.selected = true);
        }
    });
});
</script>
