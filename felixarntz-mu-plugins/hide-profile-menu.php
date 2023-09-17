<?php
/**
 * Plugin Name: Hide Profile Menu
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Hides the Profile submenu item and, if applicable, menu item in favor of link in account menu.
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

		// If full Users menu is present, remove Profile submenu item.
		if ( isset( $menu[70][2] ) && 'users.php' === $menu[70][2] ) {
			foreach ( $submenu['users.php'] as $index => $submenu_item ) {
				if ( 'profile.php' === $submenu_item[2] ) {
					unset( $submenu['users.php'][ $index ] );
					break;
				}
			}
			return;
		}

		// Otherwise, remove the entire Profile menu item.
		if ( isset( $menu[70][2] ) && 'profile.php' === $menu[70][2] ) {
			foreach ( $submenu['profile.php'] as $index => $submenu_item ) {
				if ( 'profile.php' === $submenu_item[2] ) {
					continue;
				}

				// If there are any extra items, move them under Settings.
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$submenu['options-general.php'][12] = $submenu_item;
			}
			unset( $menu[70] );
			unset( $submenu['profile.php'] );
		}
	},
	100
);
