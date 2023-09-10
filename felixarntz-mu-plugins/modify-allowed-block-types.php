<?php
/**
 * Plugin Name: Modify Allowed Block Types
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Modifies the block types allowed in the block editor.
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

add_filter(
	'allowed_block_types_all',
	static function ( $block_types, $context ) {
		$config = Shared\Config::instance();

		$allowed = array(
			$config->get( 'allowed_block_types_all', array() ),
			$config->get( "allowed_block_types_{$context->name}", array() ),
		);
		if ( isset( $context->post->post_type ) ) {
			$allowed[] = $config->get( "allowed_block_types_for_post_type_{$context->post->post_type}", array() );
		}
		$allowed = array_filter( $allowed );

		if ( ! $allowed ) {
			return $block_types;
		}

		/*
		 * If $block_types is already an array, rely on it.
		 * Otherwise, use the list of all registered block types.
		 */
		if ( is_array( $block_types ) ) {
			$block_map = array_fill_keys( $block_types, true );
		} else {
			$block_map = array_map(
				'__return_true',
				\WP_Block_Type_Registry::get_instance()->get_all_registered()
			);
		}

		// Amend the default block map with the configured overrides.
		foreach ( $allowed as $allow_map ) {
			// If an indexed array, transform it to a block types map with each type enabled.
			if ( isset( $allow_map[0] ) ) {
				$allow_map = array_fill_keys( $allow_map, true );
			}
			$block_map = array_merge( $block_map, $allow_map );
		}

		$block_types = array_keys( array_filter( $block_map ) );

		return $block_types;
	},
	10,
	2
);
