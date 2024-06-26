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

/**
 * Filter callback to reset the edit.php entry of the $_wp_submenu_nopriv global.
 *
 * @param mixed $passthrough Filter value.
 * @return mixed Unaltered $passthrough value.
 */
function reset_wp_submenu_nopriv_edit_entry_filter( $passthrough ) {
	global $_wp_submenu_nopriv, $_felixarntz_mu_orig_submenu_nopriv;

	if ( isset( $_felixarntz_mu_orig_submenu_nopriv ) ) {
		$_wp_submenu_nopriv = $_felixarntz_mu_orig_submenu_nopriv; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		unset( $_felixarntz_mu_orig_submenu_nopriv );
	}

	// Remove this filter to prevent it from running again, should this hook be called multiple times.
	remove_filter( 'add_menu_classes', __NAMESPACE__ . '\\reset_wp_submenu_nopriv_edit_entry_filter' );

	return $passthrough;
}

/*
 * This is a workaround to prevent the posts from being displayed in the admin menu.
 * It is necessary because the user_can_access_admin_page() function returns false for any post type screen just
 * because the 'post' post type is disabled. This is happening due to all these screens using the 'edit.php' page, but
 * only for the 'post' post type it is used without any additional query parameters.
 *
 * The filters 'custom_menu_order' and 'add_menu_classes' are used to temporarily alter the relevant global checked
 * because they are the two hooks closest to the actual check that is performed in user_can_access_admin_page().
 */
add_filter(
	'custom_menu_order',
	static function ( $passthrough ) {
		global $pagenow, $typenow, $_wp_submenu_nopriv, $_felixarntz_mu_orig_submenu_nopriv;

		if ( ( 'edit.php' === $pagenow || 'post-new.php' === $pagenow ) && 'post' !== $typenow && '' !== $typenow ) {
			$_felixarntz_mu_orig_submenu_nopriv = $_wp_submenu_nopriv;
			unset( $_wp_submenu_nopriv[ $pagenow ][ $pagenow ] );
			foreach ( array_keys( $_wp_submenu_nopriv ) as $key ) {
				unset( $_wp_submenu_nopriv[ $key ][ $pagenow ] );
			}
			add_filter( 'add_menu_classes', __NAMESPACE__ . '\\reset_wp_submenu_nopriv_edit_entry_filter' );
		}

		return $passthrough;
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
