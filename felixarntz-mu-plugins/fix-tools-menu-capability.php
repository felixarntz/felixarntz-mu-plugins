<?php
/**
 * Plugin Name: Fix Tools Menu Capability
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Ensures that the Tools menu is only shown if the user has the capabilities to do something with it.
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
		global $menu, $submenu;

		// Bail if additional cards may be present under "Available Tools".
		if ( has_action( 'tool_box' ) ) {
			return;
		}

		if ( isset( $menu[75] ) && 'tools.php' === $menu[75][2] ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$menu[75][1] = 'import';
			if ( isset( $submenu['tools.php'][5] ) && 'tools.php' === $submenu['tools.php'][5][2] ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$submenu['tools.php'][5][1] = 'import';
			}
		}
	},
	PHP_INT_MAX
);
