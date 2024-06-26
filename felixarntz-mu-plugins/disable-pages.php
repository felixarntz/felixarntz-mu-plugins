<?php
/**
 * Plugin Name: Disable Pages
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables pages.
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
	'map_meta_cap',
	static function ( $caps, $cap ) {
		switch ( $cap ) {
			case 'edit_pages':
			case 'delete_pages':
			case 'read_private_pages':
			case 'edit_private_pages':
			case 'delete_private_pages':
			case 'edit_published_pages':
			case 'delete_published_pages':
			case 'edit_others_pages':
			case 'delete_others_pages':
			case 'publish_pages':
				$caps[] = 'do_not_allow';
				break;
		}
		return $caps;
	},
	10,
	2
);

add_action(
	'pre_get_posts',
	static function ( $query ) {
		$post_types = $query->get( 'post_type' );
		if ( is_array( $post_types ) ) {
			$key = array_search( 'page', $post_types, true );
			if ( false !== $key ) {
				unset( $post_types[ $key ] );
				$query->set( 'post_type', array_values( $post_types ) );
			}
		}
	}
);

add_filter(
	'posts_results',
	static function ( $posts, $query ) {
		$post_type = $query->get( 'post_type' );
		if ( is_array( $post_type ) && in_array( 'page', $post_type, true ) ) {
			return array_filter(
				$posts,
				static function ( $post ) {
					return ! $post instanceof WP_Post || 'page' !== $post->post_type;
				}
			);
		}

		if ( is_string( $post_type ) && 'page' === $post_type ) {
			return array();
		}

		return $posts;
	},
	10,
	2
);
