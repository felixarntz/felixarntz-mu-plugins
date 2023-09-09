<?php
/**
 * Plugin Name: Disable Legacy CSS
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Removes legacy CSS from certain widgets and shortcodes from wp_head output.
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

add_filter( 'show_recent_comments_widget_style', '__return_false' );
add_filter( 'use_default_gallery_style', '__return_false' );
