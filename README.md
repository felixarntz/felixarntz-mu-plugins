# Felix Arntz MU Plugins

My collection of MU plugins in individual files within a subdirectory, fully configurable in a maintainable way which allows for automatic updates. [Learn more about MU plugins.](https://developer.wordpress.org/advanced-administration/plugins/mu-plugins/)

1. [Context](#context)
2. [Project structure](#project-structure)
3. [Quick start](#quick-start)
4. [Included features](#included-features)
5. [Alternative usage](#alternative-usage)
6. [License](#license)

## Context

I have been using MU plugins on my personal sites for many years, and I thought some may find them useful as well. I am aware that several individuals and companies have open-sourced some of their MU plugins, but I wanted to go a step further than that by making them actually reusable beyond just my own specific site needs.

With this repository I came up with an approach that allows you to use any of my MU plugins in a way that allows you to customize them and even apply updates to them, without having to manually copy files every time.

## Project structure

At a high level, the project is made up by three components:
* The individual MU plugin files `felixarntz-mu-plugins/*.php`, where each file is for a single standalone feature.
* Some shared utility files `felixarntz-mu-plugins/shared/*.php`, which some of the individual MU plugin files are using.
* The overarching loader file `felixarntz-mu-plugins.php`, which loads and configures the individual MU plugin files that you would like to use.

## Quick start

### Installation

There are several alternative ways to use this project, depending on your needs and flexibility. In any case, the project needs to be placed in your `wp-content/mu-plugins` directory. If your site doesn't have such a directory yet, you can simply create it.

If you use [Composer](https://getcomposer.org/) to manage your site's dependencies:
```
composer require felixarntz/felixarntz-mu-plugins
```

Otherwise, you can clone the repository:
```
git clone https://github.com/felixarntz/felixarntz-mu-plugins.git wp-content/mu-plugins/felixarntz-mu-plugins
```

Or, if you prefer to go fully manual, you can download a ZIP of the repository and extract it into your `wp-content/mu-plugins` directory.

After installing the project, you need to copy the `wp-content/mu-plugins/felixarntz-mu-plugins/felixarntz-mu-plugins.php` file one level up, i.e. to `wp-content/mu-plugins/felixarntz-mu-plugins.php`. Afterwards, you need to edit your copy of the file, specifically the [`Loader::FILES_DIR` constant](felixarntz-mu-plugins.php#L38), to point to the correct location of the `felixarntz-mu-plugins` directory containing the individual MU plugin files. You should furthermore edit that file to customize which features you want to load and to configure them (see next section).

### Configuration

The project includes many features, enhancements, and tweaks, some of which are more opinionated than others. You'll most certainly want to customize which features are loaded for your site and how they are configured. You can do so by tweaking your own copy of the loader file `wp-content/mu-plugins/felixarntz-mu-plugins.php`.

The class in the file contains two arrays that are intended to be modified:
* The indexed array returned by the `Loader::files_allowlist()` method should contain the list of MU plugin PHP file names (i.e. features) which should be loaded.
* The associative array returned by the `Loader::config()` method should contain your preferred configuration for the features.

Both methods are initially populated with all the available MU plugin files and configuration variables respectively, so that it's easy to see what is available. Since the arrays are returned by methods, feel free to use simple conditional logic to contextually set different configurations. For example, if you're using this project in a WordPress Multisite, you could return different arrays depending on which site is being accessed ([`get_site()`](https://developer.wordpress.org/reference/functions/get_site/)).

Please see [the class's inline documentation](felixarntz-mu-plugins.php) for additional information on how to make those modifications.

### Updates

To apply updates to the project later, if you use Composer, a simple `composer update` will do it. Otherwise, you need to update the `wp-content/mu-plugins/felixarntz-mu-plugins` directory with the latest version, either via `git pull` from within the directory, or by downloading the latest ZIP and replacing the directory with its contents.

## Included features

Each feature is implemented within a single MU plugin file. Note that the features definitely vary in how opinionated there are, so please use and adjust to taste. Even I myself am not using all of these features for every site, so I encourage you to pick and choose what works for you.

The following tables list all features available as part of this project, grouped by what kind of feature it is.

### Admin UI enhancements

| MU plugin feature | Description | Config variables |
| ----------------- | ----------- | ---------------- |
| [Add Admin Color Scheme Branding](felixarntz-mu-plugins/add-admin-color-scheme-branding.php) | Adds an admin color scheme reflecting the site specific brand colors. | `admin_color_scheme_base_color`<br>`admin_color_scheme_icon_color`<br>`admin_color_scheme_text_color`<br>`admin_color_scheme_highlight_color`<br>`admin_color_scheme_accent_color`<br>`admin_color_scheme_link_color`<br>`admin_color_scheme_enforced` |
| [Add Login Branding](felixarntz-mu-plugins/add-login-branding.php) | Adds site specific branding to the login page. | `login_highlight_color`<br>`login_highlight_color_hover`<br>`login_header_image_url`<br>`login_header_image_size` |
| [Clean Plugin Menus](felixarntz-mu-plugins/clean-plugin-menus.php) | Cleans up top level menu items from plugins in WP Admin. | `feedback_menu_title`<br>`insights_menu_title`<br>`move_plugin_menus` |
| [Fix Tools Menu Capability](felixarntz-mu-plugins/fix-tools-menu-capability.php) | Ensures that the Tools menu is only shown if the user has the capabilities to do something with it. | |
| [Hide Dashboard](felixarntz-mu-plugins/hide-dashboard.php) | Hides the WordPress dashboard if no additional submenu pages are added to it. | `replace_dashboard_startup_screen` |
| [Hide Profile Menu](felixarntz-mu-plugins/hide-profile-menu.php) | Hides the Profile submenu item and, if applicable, menu item in favor of link in account menu. | |
| [Modernize Account Menu Style](felixarntz-mu-plugins/modernize-account-menu-style.php) | Modifies the styling of the account menu in the admin bar to display a larger circled avatar image. | |
| [Prevent Custom Menu Order](felixarntz-mu-plugins/prevent-custom-menu-order.php) | Forces the custom menu order filter to disabled which tends to be used by plugins to put themselves to the top of the admin menu. | |
| [Remove Add New Submenu Links](felixarntz-mu-plugins/remove-add-new-submenu-links.php) | Removes all the Add New submenu items in the admin. | |
| [Remove Dashboard Widgets](felixarntz-mu-plugins/remove-dashboard-widgets.php) | Removes all default widgets from the WordPress dashboard. | `remove_dashboard_widgets`<br>`remove_default_dashboard_widgets` |
| [Show Update Notification With Disallow File Mods](felixarntz-mu-plugins/show-update-notifications-with-disallow-file-mods.php) | Shows plugin and theme update notifications even when the `DISALLOW_FILE_MODS` constant is set to true. | |
| [Simplify Themes Menu](felixarntz-mu-plugins/simplify-themes-menu.php) | Simplifies the Themes Menu to be purely about editing if the current user cannot switch themes. | |
| [Use Content Menu](felixarntz-mu-plugins/use-content-menu.php) | Moves all post type admin menus into a single Content menu. | `indent_content_menu_taxonomies` |

### Block editor modifications

| MU plugin feature | Description | Config variables |
| ----------------- | ----------- | ---------------- |
| [Add Block Editor Capabilities](felixarntz-mu-plugins/add-block-editor-capabilities.php) | Adds dedicated user capabilities for editing block editor features like block colors, typography, or layout. | `add_edit_colors_capability`<br>`add_edit_layout_capability`<br>`add_edit_typography_capability`<br>`grant_capabilities_via_edit_theme_options` |
| [Disable Block Editor Fullscreen Mode](felixarntz-mu-plugins/disable-block-editor-fullscreen-mode.php) | Disables the block editor's full screen mode by default. | |
| [Disable Custom Block Colors Gradients Font Sizes](felixarntz-mu-plugins/disable-custom-block-colors-gradients-font-sizes.php) | Disables custom colors, custom gradients, custom font sizes etc. for the block editor to enforce a uniform style. | |
| [Modify Allowed Block Types](felixarntz-mu-plugins/modify-allowed-block-types.php) | Modifies the block types allowed in the block editor. | `allowed_block_types_all`<br>`allowed_block_types_{$context}`<br>`allowed_block_types_post_type_{$post_type}`<br>`disallowed_block_types_all`<br>`disallowed_block_types_{$context}`<br>`disallowed_block_types_post_type_{$post_type}` |
| [Modify Block Patterns](felixarntz-mu-plugins/modify-block-patterns.php) | Modifies which block patterns are available, also allowing to provide custom block pattern directories. | `disable_core_patterns`<br>`disable_remote_patterns`<br>`custom_pattern_directories` |

### Disabling core functionality

| MU plugin feature | Description | Config variables |
| ----------------- | ----------- | ---------------- |
| [Disable Auto Updates](felixarntz-mu-plugins/disable-auto-updates.php) | Disables all auto updates. | |
| [Disable Comments](felixarntz-mu-plugins/disable-comments.php) | Disables comments. | |
| [Disable Pages](felixarntz-mu-plugins/disable-pages.php) | Disables pages. | |
| [Disable Pingbacks](felixarntz-mu-plugins/disable-pingbacks.php) | Disables pingbacks and trackbacks. | |
| [Disable Post Categories](felixarntz-mu-plugins/disable-post-categories.php) | Disables using and assigning categories for posts (and other post types). | |
| [Disable Post Tags](felixarntz-mu-plugins/disable-post-tags.php) | Disables using and assigning tags for posts (and other post types). | |
| [Disable Posts](felixarntz-mu-plugins/disable-posts.php) | Disables posts. | |
| [Disable XML-RPC](felixarntz-mu-plugins/disable-xmlrpc.php) | Disables XML-RPC access to the site. | |

### Cleanup of `wp_head`

| MU plugin feature | Description | Config variables |
| ----------------- | ----------- | ---------------- |
| [Disable Emoji](felixarntz-mu-plugins/disable-emoji.php) | Removes emoji script and related logic. | |
| [Disable Legacy CSS](felixarntz-mu-plugins/disable-legacy-css.php) | Removes legacy CSS from certain widgets and shortcodes from wp_head output. | |
| [Disable RSS Links](felixarntz-mu-plugins/disable-rss-links.php) | Removes RSS feed links from wp_head output. | |
| [Obscure WP Head](felixarntz-mu-plugins/obscure-wp-head.php) | Removes useless WordPress indicators from wp_head output. | `remove_wp_head_rest_references`<br>`remove_wp_head_oembed_references` |

### Performance optimization

| MU plugin feature | Description | Config variables |
| ----------------- | ----------- | ---------------- |
| [Bulk Edit Defer Term Counting](felixarntz-mu-plugins/bulk-edit-defer-term-counting.php) | Defers term counting when bulk editing to avoid slow queries for each post updated. | |
| [Optimize Last Post Modified](felixarntz-mu-plugins/optimize-lastpostmodified.php) | Optimizes the logic to get last post modified to avoid database queries for better performance. | |
| [Optimize Meta Table Schema](felixarntz-mu-plugins/optimize-meta-table-schema.php) | Optimizes performance of the meta database tables by adding an index to the meta_value field. | |

### Miscellaneous

| MU plugin feature | Description | Config variables |
| ----------------- | ----------- | ---------------- |
| [Add Client Role](felixarntz-mu-plugins/add-client-role.php) | Adds a role for clients with additional capabilities than editors, but not quite admin. | `client_role_display_name`<br>`client_role_additional_caps` |
| [Disable Non Production Indexing](felixarntz-mu-plugins/disable-non-production-indexing.php) | Ensures that the site is not indexable in a non-production environment. | |
| [Make Site Private](felixarntz-mu-plugins/make-site-private.php) | Makes the entire site private so that only logged-in users can see the content. | |
| [Modify Allowed MIME Types](felixarntz-mu-plugins/modify-allowed-mime-types.php) | Modifies the MIME types allowed for upload in the media library. | `allowed_mime_types`<br>`disallowed_mime_types` |
| [Modify REST Root](felixarntz-mu-plugins/modify-rest-root.php) | Modifies the REST API root to a different one, by default using api. | `rest_root` |
| [Use Ambiguous Login Error](felixarntz-mu-plugins/use-ambiguous-login-error.php) | Modifies the error messages for a failed login attempt to be more ambiguous. | |

## Alternative usage

It is **recommended** that you use the approach from the quick start section above, where you install the project into its own directory within your `wp-content/mu-plugins` folder, and then copy the `felixarntz-mu-plugins.php` file from the project one level up. This has multiple benefits:
* You can apply updates the project at any point by simply replacing the directory with its newer version.
* You can customize the configuration via the copied `wp-content/mu-plugins/felixarntz-mu-plugins.php` file without losing these customizations when you update.
* (Optional) If you use [Composer](https://getcomposer.org/) to manage your site's dependencies, you can handle updates automatically by including the project in your `composer.json` file.

Alternatively, you can put the `felixarntz-mu-plugins` directory as well as the `felixarntz-mu-plugins.php` file from the project directly into your `wp-content/mu-plugins` folder. While this approach still allows you to update and customize, it makes the process a bit more complicated as you have to manually only replace the `felixarntz-mu-plugins` directory within the project so that you don't overwrite your customizations to the `felixarntz-mu-plugins.php` file. Last but not least, this approach doesn't work if you want to be able to update the project using Composer. So there are no benefits to this approach.

If you don't want to use the entire project, you could also pick individual files from within the `felixarntz-mu-plugins` directory, plus the `felixarntz-mu-plugins/shared` directory, and put them directly into your `wp-content/mu-plugins` folder. However, this approach is **not recommended**. While it may seem like a lightweight solution if you only need a few of the features, it really makes maintenance unnecessarily complicated without any benefit, unless you _never_ plan to update those files. The project only loads the files that you want to be loaded anyway, so the mere presence of files with features you don't need doesn't have any notable memory or performance impact.

## License

This plugin is free software, and is released under the terms of the GNU General Public License version 2 or (at your option) any later version. See [LICENSE](/LICENSE) for complete license.
