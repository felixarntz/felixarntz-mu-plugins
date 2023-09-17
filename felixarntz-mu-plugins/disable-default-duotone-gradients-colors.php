<?php
/**
 * Plugin Name: Disable Default Duotone Gradients Colors
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables the default duotone, default gradients, default colors, etc. for the block editor.
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

add_filter(
	'wp_theme_json_data_default',
	static function ( $wp_theme_json_data ) {
		$wp_theme_json_data->update_with(
			array(
				'version'  => \WP_Theme_JSON::LATEST_SCHEMA,
				'settings' => array(
					'color' => array(
						'defaultDuotone'   => false,
						'defaultGradients' => false,
						'defaultPalette'   => false,
					),
				),
			)
		);

		return $wp_theme_json_data;
	}
);
