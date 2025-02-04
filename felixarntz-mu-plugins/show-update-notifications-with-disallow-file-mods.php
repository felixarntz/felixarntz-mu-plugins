<?php
/**
 * Plugin Name: Show Update Notification With Disallow File Mods
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Shows plugin and theme update notifications even when the `DISALLOW_FILE_MODS` constant is set to true.
 * Author: Felix Arntz
 * Author URI: https://felix-arntz.me
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: felixarntz-mu-plugins
 *
 * @package felixarntz-mu-plugins
 */

namespace Felix_Arntz\MU_Plugins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action(
	'admin_init',
	static function () {
		if ( ! defined( 'DISALLOW_FILE_MODS' ) || ! DISALLOW_FILE_MODS ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			add_action(
				'load-plugins.php',
				static function () {
					// This logic is originally run via `wp_plugin_update_rows()`.
					$plugins = get_site_transient( 'update_plugins' );

					if ( isset( $plugins->response ) && is_array( $plugins->response ) ) {
						$plugins = array_keys( $plugins->response );

						foreach ( $plugins as $plugin_file ) {
							add_action( "after_plugin_row_{$plugin_file}", 'wp_plugin_update_row', 10, 2 );
						}
					}
				},
				20
			);
		}

		if ( ! current_user_can( 'update_themes' ) ) {
			add_action(
				'load-themes.php',
				static function () {
					// This logic is originally run via `wp_theme_update_rows()`.
					$themes = get_site_transient( 'update_themes' );

					if ( isset( $themes->response ) && is_array( $themes->response ) ) {
						$themes = array_keys( $themes->response );

						foreach ( $themes as $theme ) {
							add_action( "after_theme_row_{$theme}", 'wp_theme_update_row', 10, 2 );
						}
					}
				},
				20
			);
		}
	}
);
