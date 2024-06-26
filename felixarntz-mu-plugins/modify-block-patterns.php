<?php
/**
 * Plugin Name: Modify Block Patterns
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Modifies which block patterns are available, also allowing to provide custom block pattern directories.
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

require_once __DIR__ . '/shared/loader.php';

add_action(
	'init',
	static function () {
		$config = Shared\Config::instance();

		// Note: Disabling core patterns also disables remote patterns.
		$disable_core_patterns   = $config->get( 'disable_core_patterns', false );
		$disable_remote_patterns = $config->get( 'disable_remote_patterns', false );
		if ( $disable_core_patterns ) {
			remove_theme_support( 'core-block-patterns' );
		}
		if ( $disable_remote_patterns ) {
			add_filter( 'should_load_remote_block_patterns', '__return_false' );
		}

		$custom_pattern_directories = array_filter( $config->get( 'custom_pattern_directories', array() ) );
		foreach ( $custom_pattern_directories as $custom_pattern_directory ) {
			if ( is_array( $custom_pattern_directory ) ) {
				if ( ! isset( $custom_pattern_directory['dir'] ) ) {
					continue;
				}
				$pattern_dir = $custom_pattern_directory['dir'];
				$version     = $custom_pattern_directory['version'] ?? '';
				$text_domain = $custom_pattern_directory['text_domain'] ?? '';
			} else {
				$pattern_dir = $custom_pattern_directory;
				$version     = '';
				$text_domain = '';
			}

			$parser = new Shared\Block_Pattern_File_Parser( $pattern_dir, $version, $text_domain );
			$parser->register_block_patterns();
		}
	},
	9
);
