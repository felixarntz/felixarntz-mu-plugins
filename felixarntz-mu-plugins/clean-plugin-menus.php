<?php
/**
 * Plugin Name: Clean Plugin Menus
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Cleans up top level menu items from plugins in WP Admin.
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
		$config     = Shared\Config::instance();
		$admin_menu = Shared\Admin_Menu::instance();

		$migrate_item = function ( $submenu_page, $menu_page, $target_menu ) use ( $config, $admin_menu ) {
			global $wp_filter;

			static $insights_menu_slug = null;

			switch ( $target_menu ) {
				case 'insights':
					if ( ! $insights_menu_slug ) {
						$menu_title = $config->get( 'insights_menu_title', '' );
						if ( ! $menu_title ) {
							$menu_title = __( 'Insights', 'felixarntz-mu-plugins' );
						}
						add_menu_page(
							$menu_title,
							$menu_title,
							'read',
							'felixarntz-insights',
							static function () {},
							'dashicons-analytics',
							30
						);
						$insights_menu_slug = 'felixarntz-insights';
					}
					$new_menu_slug = $insights_menu_slug;
					break;
				case 'tools':
					$new_menu_slug = 'tools.php';
					break;
				case 'settings':
					$new_menu_slug = 'options-general.php';
					break;
			}

			if ( ! isset( $new_menu_slug ) ) {
				return false;
			}

			$hook_submenu_slug = plugin_basename( $submenu_page[2] );
			$hook_menu_slug    = plugin_basename( $menu_page[2] );
			$old_hookname      = get_plugin_page_hookname( $hook_submenu_slug, $hook_menu_slug );

			if ( ! $admin_menu->move_submenu_page( $menu_page[2], $submenu_page[2], $new_menu_slug ) ) {
				return false;
			}

			$new_title = sprintf(
				/* translators: 1: menu title, 2: submenu title */
				__( '%1$s: %2$s', 'felixarntz-mu-plugins' ),
				trim( explode( '<span class="update-plugins', $menu_page[0] )[0] ),
				$submenu_page[0]
			);
			$admin_menu->update_submenu_page_title( $new_menu_slug, $submenu_page[2], $new_title );

			// Copy old page hook so that links to the admin page work correctly.
			if ( isset( $wp_filter[ $old_hookname ] ) ) {
				$new_hookname = get_plugin_page_hookname( $hook_submenu_slug, $new_menu_slug );
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$wp_filter[ $new_hookname ] = $wp_filter[ $old_hookname ];
			}

			return true;
		};

		$move_plugin_menus = array_merge(
			array(
				'googlesitekit-splash'    => 'insights',
				'googlesitekit-dashboard' => 'insights',
				'googlesitekit-settings'  => 'settings',
				'jetpack'                 => 'insights',
				'jetpack#/dashboard'      => 'insights',
				'my-jetpack'              => 'settings',
				'jetpack#/settings'       => 'settings',
				'wpseo_dashboard'         => 'hide',
				'wpseo_page_settings'     => 'settings',
				'wpseo_integrations'      => 'hide',
				'wpseo_tools'             => 'tools',
				'wpseo_page_academy'      => 'hide',
				'wpseo_licenses'          => 'hide',
				'wpseo_workouts'          => 'hide',
				'wpseo_redirects'         => 'hide',
				'wpseo_page_support'      => 'hide',
			),
			$config->get( 'move_plugin_menus', array() )
		);

		$moved = array();
		foreach ( $move_plugin_menus as $menu_slug => $target_menu ) {
			$menu_page = $admin_menu->get_menu_page( $menu_slug );
			if ( ! $menu_page ) {
				continue;
			}

			$submenu_pages = $admin_menu->get_submenu_pages( $menu_slug );
			foreach ( $submenu_pages as $submenu_page ) {
				if ( isset( $move_plugin_menus[ $submenu_page[2] ] ) ) {
					if ( 'hide' === $move_plugin_menus[ $submenu_page[2] ] ) {
						$admin_menu->remove_submenu_page( $menu_page[2], $submenu_page[2] );
						continue;
					}

					if ( $migrate_item( $submenu_page, $menu_page, $move_plugin_menus[ $submenu_page[2] ] ) ) {
						$moved[ $submenu_page[2] ] = $menu_page[2];
					}
				}
			}
		}

		/*
		 * Temporarily modify the $pagenow global to set it to the original parent page slug so that the other globals
		 * will be set as if the plugin page was still in its original location. It cannot be set to the original
		 * parent file as that would not be registered with the correct page hook.
		 * Afterwards, hook into the load process to reset $pagenow to the original parent page file.
		 */
		add_action(
			'admin_init',
			function () use ( $moved ) {
				global $pagenow, $plugin_page;

				if ( isset( $plugin_page ) && isset( $moved[ $plugin_page ] ) ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$pagenow = $moved[ $plugin_page ];

					add_action(
						'load-' . get_plugin_page_hook( $plugin_page, $pagenow ),
						function () {
							global $pagenow;

							// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							$pagenow = 'admin.php';
						}
					);
				}
			}
		);
	},
	PHP_INT_MAX
);
