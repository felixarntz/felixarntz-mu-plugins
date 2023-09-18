<?php
/**
 * Plugin Name: Prevent Custom Menu Order
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Forces the custom menu order filter to disabled which tends to be used by plugins to put themselves to the top of the admin menu.
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

add_action(
	'admin_menu',
	static function () {
		add_filter( 'custom_menu_order', '__return_false', PHP_INT_MAX );
	},
	PHP_INT_MAX
);
