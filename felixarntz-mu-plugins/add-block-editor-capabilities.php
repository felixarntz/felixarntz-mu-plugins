<?php
/**
 * Plugin Name: Add Block Editor Capabilities
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Adds dedicated user capabilities for editing block editor features like block colors, typography, or layout.
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

/**
 * Returns the configuration data for the MU plugin file.
 *
 * @return array Configuration data.
 */
function block_editor_capabilities_config() {
	static $config_data = null;

	if ( null === $config_data ) {
		$config      = Shared\Config::instance();
		$config_data = array(
			'add_edit_colors_capability'                => $config->get( 'add_edit_colors_capability', false ),
			'add_edit_layout_capability'                => $config->get( 'add_edit_layout_capability', true ),
			'add_edit_typography_capability'            => $config->get( 'add_edit_typography_capability', false ),
			'grant_capabilities_via_edit_theme_options' => $config->get( 'grant_capabilities_via_edit_theme_options', true ),
		);
	}

	return $config_data;
}

// Grant the relevant block editor capabilities to everyone that can 'edit_theme_options'.
add_filter(
	'user_has_cap',
	static function ( $allcaps ) {
		$config_data = block_editor_capabilities_config();
		if ( isset( $allcaps['edit_theme_options'] ) && $config_data['grant_capabilities_via_edit_theme_options'] ) {
			if ( $config_data['add_edit_colors_capability'] ) {
				$allcaps['edit_colors'] = $allcaps['edit_theme_options'];
			}
			if ( $config_data['add_edit_layout_capability'] ) {
				$allcaps['edit_layout'] = $allcaps['edit_theme_options'];
			}
			if ( $config_data['add_edit_typography_capability'] ) {
				$allcaps['edit_typography'] = $allcaps['edit_theme_options'];
			}
		}
		return $allcaps;
	}
);

// Disallow editing certain features for all blocks depending on the capabilities.
add_filter(
	'register_block_type_args',
	static function ( $args ) {
		$config_data = block_editor_capabilities_config();

		if ( isset( $args['supports']['color'] ) && $config_data['add_edit_colors_capability'] && ! current_user_can( 'edit_colors' ) ) {
			$args['supports']['color'] = array(
				'background' => false,
				'button'     => false,
				'caption'    => false,
				'heading'    => false,
				'link'       => false,
				'text'       => false,
			);
		}
		if ( isset( $args['supports']['spacing'] ) && $config_data['add_edit_layout_capability'] && ! current_user_can( 'edit_layout' ) ) {
			$args['supports']['spacing'] = false;
		}
		if ( isset( $args['supports']['typography'] ) && $config_data['add_edit_typography_capability'] && ! current_user_can( 'edit_typography' ) ) {
			$args['supports']['typography'] = false;
		}
		return $args;
	}
);

// Disallow editing layout in the block editor unless the current user can 'edit_layout'.
add_filter(
	'wp_theme_json_data_default',
	static function ( $wp_theme_json_data ) {
		$config_data = block_editor_capabilities_config();

		// Bail if the current user can edit the layout or if the capability is not required.
		if ( ! $config_data['add_edit_layout_capability'] || current_user_can( 'edit_layout' ) ) {
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
