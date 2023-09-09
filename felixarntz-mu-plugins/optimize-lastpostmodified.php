<?php
/**
 * Plugin Name: Optimize Last Post Modified
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Optimizes the logic to get last post modified to avoid database queries for better performance.
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

/**
 * Gets the value of a specific 'lastpostmodified' option.
 *
 * @param string $timezone  Timezone. Either 'gmt', 'server', or 'blog'.
 * @param string $post_type Optional. Post type slug, or 'any' for the overall value. Default 'any'.
 * @return mixed Option value.
 */
function get_lastpostmodified_option( $timezone, $post_type = 'any' ) {
	$option_name = sprintf( 'felixarntz_lastpostmodified_%s_%s', strtolower( $timezone ), $post_type );
	return get_option( $option_name, '' );
}

/**
 * Updates the value of a specific 'lastpostmodified' option.
 *
 * @param string $time      Timestamp to set, in 'Y-m-d H:i:s' format.
 * @param string $timezone  Timezone. Either 'gmt', 'server', or 'blog'.
 * @param string $post_type Optional. Post type slug, or 'any' for the overall value. Default 'any'.
 * @return bool True on success, false on failure.
 */
function update_lastpostmodified_option( $time, $timezone, $post_type = 'any' ) {
	$option_name = sprintf( 'felixarntz_lastpostmodified_%s_%s', strtolower( $timezone ), $post_type );
	return (bool) update_option( $option_name, $time, false );
}

/**
 * Checks whether the 'lastpostmodified' option value for the given post type is locked.
 *
 * Whenever the value is being updated, it will be locked for 30 seconds to avoid excessive database writes.
 *
 * @param string $post_type Post type slug.
 * @return bool True if the option value is locked, false otherwise.
 */
function is_lastpostmodified_option_locked( $post_type ) {
	$key = sprintf( 'felixarntz_lastpostmodified_%s_lock', $post_type );
	return false === wp_cache_add( $key, 1, false, 30 );
}

// Override 'lastpostmodified' to use value stored in option, if available.
add_filter(
	'pre_get_lastpostmodified',
	static function ( $lastpostmodified, $timezone, $post_type ) {
		$stored_lastpostmodified = get_lastpostmodified_option( $timezone, $post_type );
		if ( ! $stored_lastpostmodified ) {
			return $lastpostmodified;
		}

		return $stored_lastpostmodified;
	},
	10,
	3
);

// Update 'lastpostmodified' option values when post status is updated, unless there is an active lock.
add_action(
	'transition_post_status',
	static function ( $new_status, $old_status, $post ) {
		if ( ! in_array( 'publish', array( $old_status, $new_status ), true ) ) {
			return;
		}

		$public_post_types = get_post_types( array( 'public' => true ) );
		if ( ! in_array( $post->post_type, $public_post_types, true ) ) {
			return;
		}

		if ( is_lastpostmodified_option_locked( $post->post_type ) ) {
			return;
		}

		// Update overall value for 'any'.
		update_lastpostmodified_option( $post->post_modified_gmt, 'gmt' );
		update_lastpostmodified_option( $post->post_modified_gmt, 'server' );
		update_lastpostmodified_option( $post->post_modified, 'blog' );

		// Update value for post_type.
		update_lastpostmodified_option( $post->post_modified_gmt, 'gmt', $post->post_type );
		update_lastpostmodified_option( $post->post_modified_gmt, 'server', $post->post_type );
		update_lastpostmodified_option( $post->post_modified, 'blog', $post->post_type );
	},
	10,
	3
);
