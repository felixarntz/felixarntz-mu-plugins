<?php
/**
 * Plugin Name: Disable RSS Links
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Removes RSS feed links from wp_head output.
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

remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );
