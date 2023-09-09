<?php
/**
 * Plugin Name: Optimize Meta Table Schema
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Optimizes performance of the meta database tables by adding an index to the meta_value field.
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
	'dbdelta_create_queries',
	static function ( $queries ) {
		global $wpdb;

		$meta_tables = array_fill_keys(
			array(
				$wpdb->postmeta,
				$wpdb->termmeta,
				$wpdb->commentmeta,
				$wpdb->usermeta,
			),
			true
		);

		foreach ( $queries as $k => $q ) {
			// Replace meta_key index with one that indexes meta_value as well.
			if ( preg_match( '|CREATE TABLE ([^ ]*)|', $q, $matches ) && isset( $meta_tables[ $matches[1] ] ) ) {
				$queries[ $k ] = str_replace(
					'KEY meta_key (meta_key(191))',
					'KEY `felixarntz_meta_key_value` (`meta_key`(191), `meta_value`(100))',
					$q
				);
			}
		}

		return $queries;
	}
);
