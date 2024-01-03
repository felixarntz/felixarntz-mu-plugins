<?php
/**
 * Plugin Name: Obscure WP Head
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Removes useless WordPress indicators from wp_head output.
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

add_filter( 'the_generator', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// The following removals are conditional as there is a reasonable benefit to keeping them.
add_action(
	'wp_loaded',
	static function () {
		$config        = Shared\Config::instance();
		$remove_rest   = $config->get( 'remove_wp_head_rest_references', false );
		$remove_oembed = $config->get( 'remove_wp_head_oembed_references', false );
		if ( $remove_rest ) {
			remove_action( 'wp_head', 'rest_output_link_wp_head' );
		}
		if ( $remove_oembed ) {
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		}
	}
);
