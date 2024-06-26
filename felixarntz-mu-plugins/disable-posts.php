<?php
/**
 * Plugin Name: Disable Posts
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables posts.
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
			case 'edit_posts': // While disabling this capability makes sense, it requires a workaround (see below).
			case 'delete_posts':
			case 'read_private_posts':
			case 'edit_private_posts':
			case 'delete_private_posts':
			case 'edit_published_posts':
			case 'delete_published_posts':
			case 'edit_others_posts':
			case 'delete_others_posts':
			case 'publish_posts':
				$caps[] = 'do_not_allow';
				break;
		}
		return $caps;
	},
	10,
	2
);

/*
 * These items have to be removed early.
 * Otherwise their existence will prevent other post type menu items from being accessible.
 */
add_action(
	'_admin_menu',
	static function () {
		remove_submenu_page( 'edit.php', 'edit.php' );
		remove_submenu_page( 'edit.php', 'post-new.php' );
		remove_menu_page( 'edit.php' );
	}
);

add_action(
	'pre_get_posts',
	static function ( $query ) {
		$post_types = $query->get( 'post_type' );
		if ( is_array( $post_types ) ) {
			$key = array_search( 'post', $post_types, true );
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
		if ( is_array( $post_type ) && in_array( 'post', $post_type, true ) ) {
			return array_filter(
				$posts,
				static function ( $post ) {
					return ! $post instanceof WP_Post || 'post' !== $post->post_type;
				}
			);
		}

		if ( is_string( $post_type ) && 'post' === $post_type ) {
			return array();
		}

		return $posts;
	},
	10,
	2
);

add_filter(
	'pre_option_show_on_front',
	static function () {
		return 'page';
	}
);
add_filter(
	'pre_option_page_for_posts',
	static function () {
		return 0;
	}
);
