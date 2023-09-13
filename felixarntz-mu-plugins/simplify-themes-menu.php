<?php
/**
 * Plugin Name: Simplify Themes Menu
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Simplifies the Themes Menu to be purely about editing if the current user cannot switch themes.
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

		if ( current_user_can( 'switch_themes' ) ) {
			return;
		}

		// Only for classic themes, the theme code editor may also appear as a Themes submenu item.
		if ( ! wp_is_block_theme() && current_user_can( 'edit_themes' ) ) {
			return;
		}

		$to_remove = array(
			__( 'Menus', 'default' )      => true,
			__( 'Header', 'default' )     => true,
			__( 'Background', 'default' ) => true,
		);
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$submenu['themes.php'] = array_filter(
			$submenu['themes.php'],
			static function ( $submenu_item ) use ( $to_remove ) {
				if ( 'themes.php' === $submenu_item[2] ) {
					return false;
				}
				return ! isset( $to_remove[ $submenu_item[0] ] );
			}
		);

		/*
		 * If there's only one item left in the Themes menu, it's either the Site Editor or Customizer, so make it the
		 * only item.
		 */
		if ( count( $submenu['themes.php'] ) === 1 && isset( $menu[60][2] ) && 'themes.php' === $menu[60][2] ) {
			reset( $submenu['themes.php'] );
			$index = key( $submenu['themes.php'] );
			if ( _x( 'Editor', 'site editor menu item', 'default' ) === $submenu['themes.php'][ $index ][0] ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$submenu['themes.php'][ $index ][0] = __( 'Site Editor', 'default' );
			}
			for ( $i = 0; $i < 3; $i++ ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$menu[60][ $i ] = $submenu['themes.php'][ $index ][ $i ];
			}
		}
	}
);
