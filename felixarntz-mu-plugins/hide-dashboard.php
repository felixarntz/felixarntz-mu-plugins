<?php
/**
 * Plugin Name: Hide Dashboard
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Hides the WordPress dashboard if no additional submenu pages are added to it.
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

// Hide the dashboard menu if it only contains the default WordPress submenu items.
add_action(
	'admin_menu',
	static function () {
		global $menu;

		$admin_menu = Shared\Admin_Menu::instance();
		if ( $admin_menu->get_menu_page( 'index.php' ) ) {
			$submenu_page_count = $admin_menu->get_submenu_page_count( 'index.php' );

			// Migrate the other WordPress submenu items to options pages.
			if ( 2 === $submenu_page_count ) {
				$extra_submenu_page = $admin_menu->get_submenu_page( 'index.php', 'update-core.php' );
				if ( ! $extra_submenu_page ) {
					$extra_submenu_page = $admin_menu->get_submenu_page( 'index.php', 'my-sites.php' );
				}
				if ( $extra_submenu_page ) {
					if ( ! $admin_menu->move_submenu_page( 'index.php', $extra_submenu_page[2], 'options-general.php', 12 ) ) {
						return;
					}
					$submenu_page_count--;
				}
			}

			// If now there is only the dashboard left, hide the entire dashboard menu.
			if ( $submenu_page_count < 2 ) {
				$admin_menu->remove_menu_page( 'index.php' );

				// If there is no other menu above the first separator, hide the separator as well.
				$sorted_menu = $menu;
				ksort( $sorted_menu );
				if ( key( $sorted_menu ) === 4 ) {
					unset( $menu[4] );
				}

				// Redirect to another startup screen when index.php is hit.
				add_action(
					'admin_init',
					static function () {
						global $pagenow;

						// Do not redirect if any request parameters are present as that may break certain actions.
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( 'index.php' === $pagenow && empty( $_REQUEST ) ) {
							$config         = Shared\Config::instance();
							$startup_screen = $config->get( 'replace_dashboard_startup_screen', '' );
							if ( ! $startup_screen ) {
								$startup_screen = 'edit.php';
							}
							wp_safe_redirect( admin_url( $startup_screen ) );
							exit;
						}
					},
					100
				);
			}
		}
	},
	PHP_INT_MAX
);
