<?php
/**
 * Plugin Name: Disable Non Production Indexing
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Ensures that the site is not indexable in a non-production environment.
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

if ( wp_get_environment_type() !== 'production' && ! is_admin() ) {
	add_action( 'pre_option_blog_public', '__return_zero' );
}
