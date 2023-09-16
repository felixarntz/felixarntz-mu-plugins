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
		global $menu, $submenu;

		if ( isset( $menu[2] ) && 'index.php' === $menu[2][2] && isset( $submenu['index.php'][0] ) && count( $submenu['index.php'] ) <= 2 ) {
			// Migrate the other WordPress submenu items to options pages.
			foreach ( $submenu['index.php'] as $index => $submenu_item ) {
				if ( 'my-sites.php' === $submenu_item[2] || 'update-core.php' === $submenu_item[2] ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$submenu['options-general.php'][12] = $submenu_item;
					unset( $submenu['index.php'][ $index ] );
				}
			}

			// If now there is only the dashboard left, hide the entire dashboard menu.
			if ( count( $submenu['index.php'] ) === 1 ) {
				unset( $menu[2], $submenu['index.php'] );

				// If there is no other menu above the first separator, hide the separator as well.
				reset( $menu );
				if ( key( $menu ) === 4 ) {
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
	100
);
