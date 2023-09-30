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

require_once __DIR__ . '/shared/loader.php';

add_action(
	'admin_menu',
	static function () {
		$admin_menu = Shared\Admin_Menu::instance();

		// If full Users menu is present, remove Profile submenu item.
		if ( $admin_menu->get_menu_page( 'users.php' ) ) {
			$admin_menu->remove_submenu_page( 'users.php', 'profile.php' );
			return;
		}

		// Otherwise, remove the entire Profile menu item.
		if ( $admin_menu->get_menu_page( 'profile.php' ) ) {
			if ( $admin_menu->remove_submenu_page( 'profile.php', 'profile.php' ) ) {
				// If there are any extra items, move them under Settings.
				$submenu_page = $admin_menu->get_first_submenu_page( 'profile.php' );
				while ( $submenu_page ) {
					if ( $admin_menu->move_submenu_page( 'profile.php', $submenu_page[2], 'options-general.php' ) ) {
						$submenu_page = $admin_menu->get_first_submenu_page( 'profile.php' );
					} else {
						$submenu_page = array();
					}
				}
			}
		}
	},
	PHP_INT_MAX
);
