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

		$allowed    = array(
			$config->get( 'allowed_block_types_all', array() ),
			$config->get( "allowed_block_types_{$context->name}", array() ),
		);
		$disallowed = array(
			$config->get( 'disallowed_block_types_all', array() ),
			$config->get( "disallowed_block_types_{$context->name}", array() ),
		);
		if ( isset( $context->post->post_type ) ) {
			$allowed[]    = $config->get( "allowed_block_types_post_type_{$context->post->post_type}", array() );
			$disallowed[] = $config->get( "disallowed_block_types_post_type_{$context->post->post_type}", array() );
		}
		$allowed    = array_filter( $allowed );
		$disallowed = array_filter( $disallowed );

		// Bail without changes if no customizations are provided.
		if ( ! $allowed && ! $disallowed ) {
			return $block_types;
		}

		/*
		 * If any allowlist is provided, initialize the allowed block map empty.
		 * Otherwise, start with a list of all registered block types to then disallow certain block types from there,
		 * unless the original $block_types has already been limited, in which case that should be the starting point.
		 */
		if ( $allowed ) {
			$block_map = array();
		} elseif ( is_array( $block_types ) ) {
			$block_map = array_fill_keys( $block_types, true );
		} else {
			$block_map = array_map(
				'__return_true',
				\WP_Block_Type_Registry::get_instance()->get_all_registered()
			);
		}

		// Amend the initial block map with the configured overrides.
		foreach ( $allowed as $allowed_blocks ) {
			foreach ( $allowed_blocks as $allowed_block ) {
				$block_map[ $allowed_block ] = true;
			}
		}
		foreach ( $disallowed as $disallowed_blocks ) {
			foreach ( $disallowed_blocks as $disallowed_block ) {
				$block_map[ $disallowed_block ] = false;
			}
		}

		$block_types = array_keys( array_filter( $block_map ) );

		return $block_types;
	},
	10,
	2
);
