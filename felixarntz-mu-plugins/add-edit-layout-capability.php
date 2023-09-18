<?php
/**
 * Plugin Name: Add Edit Layout Capability
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Adds a dedicated capability for editing layout in the block editor.
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

// Grant the 'edit_layout' capability to everyone that can 'edit_theme_options'.
add_filter(
	'user_has_cap',
	static function ( $allcaps ) {
		if ( isset( $allcaps['edit_theme_options'] ) ) {
			$allcaps['edit_layout'] = $allcaps['edit_theme_options'];
		}
		return $allcaps;
	}
);

// Disallow editing layout in the block editor unless the current user can 'edit_layout'.
add_filter(
	'wp_theme_json_data_default',
	static function ( $wp_theme_json_data ) {
		// Bail if the current user can edit the layout.
		if ( current_user_can( 'edit_layout' ) ) {
			return $wp_theme_json_data;
		}

		// This only works in WordPress 6.4+.
		$wp_theme_json_data->update_with(
			array(
				'version'  => \WP_Theme_JSON::LATEST_SCHEMA,
				'settings' => array(
					'layout' => array(
						'allowEditing' => false,
					),
				),
			)
		);

		return $wp_theme_json_data;
	}
);
