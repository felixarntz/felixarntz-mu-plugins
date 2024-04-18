<?php
/**
 * Plugin Name: Felix Arntz MU Plugins
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: My collection of MU plugins in individual files within a subdirectory.
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

if ( ! class_exists( __NAMESPACE__ . '\\Loader' ) ) {
	/**
	 * Overall loader class.
	 *
	 * You can customize the configuration below according to your needs.
	 */
	class Loader {

		/**
		 * Subdirectory in which the plugin files reside.
		 *
		 * If you moved this configuration file a level up to reside outside of the
		 * root directory, you need to adjust this variable accordingly.
		 *
		 * For example to:
		 * const FILES_DIR = __DIR__ . '/felixarntz-mu-plugins/felixarntz-mu-plugins';
		 */
		const FILES_DIR = __DIR__ . '/felixarntz-mu-plugins';

		/**
		 * Returns the allowlist of which files to load.
		 *
		 * All values must be the names of PHP files within the above subdirectory.
		 * Effectively, this list acts as an on/off switch for the individual
		 * features per file.
		 *
		 * You can customize this to only load the files you need. Alternatively,
		 * leave it empty so that _all_ files are loaded.
		 *
		 * @return array List of PHP file names within the subdirectory.
		 */
		public static function files_allowlist(): array {
			return array();
		}

		/**
		 * Returns the configuration for the different features.
		 *
		 * All of these fields tweak specific behaviors within one of the feature files.
		 *
		 * You can customize these values to your liking.
		 *
		 * @return array Associative array of configuration data for the features.
		 */
		public static function config(): array {
			return array(
				'admin_color_scheme_base_color'         => '#52accc',
				'admin_color_scheme_icon_color'         => '#e5f8ff',
				'admin_color_scheme_text_color'         => '#fff',
				'admin_color_scheme_highlight_color'    => '#096484',
				'admin_color_scheme_accent_color'       => '#e1a948',
				'admin_color_scheme_link_color'         => '#0073aa',
				'allowed_block_types_all'               => array(),
				'allowed_block_types_core/edit-post'    => array(),
				'allowed_block_types_core/edit-site'    => array(),
				'allowed_block_types_post_type_page'    => array(),
				'disallowed_block_types_all'            => array(),
				'disallowed_block_types_core/edit-post' => array(),
				'disallowed_block_types_core/edit-site' => array(),
				'disallowed_block_types_post_type_page' => array(),
				'client_role_display_name'              => '',
				'client_role_additional_caps'           => array(
					'edit_theme_options',
					'update_core',
					'update_plugins',
					'update_themes',
				),
				'feedback_menu_title'                   => '',
				'indent_content_menu_taxonomies'        => false,
				'insights_menu_title'                   => '',
				'login_highlight_color'                 => '',
				'login_highlight_color_hover'           => '',
				'login_header_image_url'                => '',
				'login_header_image_size'               => '',
				'move_plugin_menus'                     => array(),
				'remove_dashboard_widgets'              => array(),
				'remove_wp_head_rest_references'        => false,
				'remove_wp_head_oembed_references'      => false,
				'replace_dashboard_startup_screen'      => 'edit.php',
				'rest_root'                             => 'api',
			);
		}

		/*
		* WARNING:
		* Do not edit anything below!
		* All configuration changes can be made above.
		*/

		/**
		 * Loads the features based on the configuration defined above.
		 *
		 * Do not edit this method. Instead, edit any of the configuration above.
		 */
		public static function load() {
			static $loaded = false;

			if ( $loaded ) {
				return;
			}
			$loaded = true;

			require_once trailingslashit( self::FILES_DIR ) . 'shared/loader.php';

			// Optional allowlist of files.
			$files_allowlist = self::files_allowlist();

			// Optional configuration for the individual files.
			$config = new Shared\Config( self::config() );
			Shared\Config::instance( $config );

			$file_loader = new Shared\File_Loader();

			// If allowlist is used, load only those files, otherwise load all files.
			if ( $files_allowlist ) {
				foreach ( $files_allowlist as $file ) {
					$file_loader->load_file( $file );
				}
				return;
			}

			$file_loader->load_all_files();
		}
	}
}

Loader::load();
