<?php
/**
 * Plugin Name: Disable Post Categories
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables using and assigning categories for posts (and other post types).
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
			case 'manage_categories':
			case 'edit_categories':
			case 'delete_categories':
			case 'assign_categories':
				$caps[] = 'do_not_allow';
		}
		return $caps;
	},
	10,
	2
);
