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

			static $feedback_menu_slug = null;
			static $insights_menu_slug = null;

			switch ( $target_menu ) {
				case 'feedback':
					if ( ! $feedback_menu_slug ) {
						$menu_title = $config->get( 'feedback_menu_title', '' );
						if ( ! $menu_title ) {
							$menu_title = __( 'Feedback', 'felixarntz-mu-plugins' );
						}
						add_menu_page(
							$menu_title,
							$menu_title,
							'read',
							'felixarntz-feedback',
							static function () {},
							'dashicons-feedback',
							30
						);
						$feedback_menu_slug = 'felixarntz-feedback';
					}
					$new_menu_slug = $feedback_menu_slug;
					break;
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
							40
						);
						$insights_menu_slug = 'felixarntz-insights';
					}
					$new_menu_slug = $insights_menu_slug;
					break;
				case 'appearance':
				case 'themes':
					$new_menu_slug = 'themes.php';
					break;
				case 'plugins':
					$new_menu_slug = 'plugins.php';
					break;
				case 'users':
					$new_menu_slug = 'users.php';
					break;
				case 'tools':
					$new_menu_slug = 'tools.php';
					break;
				case 'options':
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
			$admin_menu->update_submenu_page_menu_title( $new_menu_slug, $submenu_page[2], $new_title );
			$admin_menu->update_submenu_page_doc_title( $new_menu_slug, $submenu_page[2], $new_title );

			// Copy old page hook so that links to the admin page work correctly.
			if ( isset( $wp_filter[ $old_hookname ] ) ) {
				$new_hookname = get_plugin_page_hookname( $hook_submenu_slug, $new_menu_slug );
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$wp_filter[ $new_hookname ] = $wp_filter[ $old_hookname ];
			}

			return $new_menu_slug;
		};

		$move_plugin_menus = array_merge(
			array(
				'akismet-key-config'      => 'settings',
				'googlesitekit-splash'    => 'insights',
				'googlesitekit-dashboard' => 'insights',
				'googlesitekit-settings'  => 'settings',
				'jetpack'                 => 'insights',
				'jetpack#/dashboard'      => 'insights',
				'my-jetpack'              => 'settings',
				'jetpack#/settings'       => 'settings',
				'jetpack-boost'           => 'settings',
				'jetpack-search'          => 'hide',
				'wpcf7'                   => 'feedback',
				'wpcf7-new'               => 'hide',
				'wpcf7-integration'       => 'settings',
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
					// Do not make any change if no target set or explicitly forced to keep.
					if ( empty( $move_plugin_menus[ $submenu_page[2] ] ) || 'keep' === $move_plugin_menus[ $submenu_page[2] ] ) {
						continue;
					}

					// Simply hide the submenu page if target set to 'hide'.
					if ( 'hide' === $move_plugin_menus[ $submenu_page[2] ] ) {
						$admin_menu->remove_submenu_page( $menu_page[2], $submenu_page[2] );
						continue;
					}

					// Otherwise, attempt to move to the target menu set.
					$new_menu_slug = $migrate_item( $submenu_page, $menu_page, $move_plugin_menus[ $submenu_page[2] ] );
					if ( $new_menu_slug ) {
						$new_parent_file           = str_ends_with( $new_menu_slug, '.php' ) ? $new_menu_slug : 'admin.php';
						$moved[ $submenu_page[2] ] = array(
							'old_parent'      => $menu_page[2],
							'new_parent'      => $new_menu_slug,
							'new_parent_file' => $new_parent_file,
							'hash_children'   => array(),
						);
						if ( str_contains( $submenu_page[2], '#' ) ) {
							$unhashed_slug = explode( '#', $submenu_page[2] )[0];
							if ( isset( $moved[ $unhashed_slug ] ) ) {
								if ( 'admin.php' === $new_parent_file ) {
									$moved[ $unhashed_slug ]['new_parent']      = $new_menu_slug;
									$moved[ $unhashed_slug ]['new_parent_file'] = $new_parent_file;
								}
								$moved[ $unhashed_slug ]['hash_children'][] = $submenu_page[2];
								continue;
							}
							$moved[ $unhashed_slug ] = array(
								'old_parent'      => $menu_page[2],
								'new_parent'      => $new_menu_slug,
								'new_parent_file' => $new_parent_file,
								'hash_children'   => array( $submenu_page[2] ),
							);
						}
					}
				}
			}
		}

		add_action(
			'admin_init',
			function () use ( $moved ) {
				global $pagenow, $plugin_page;

				if ( ! isset( $plugin_page ) || ! isset( $moved[ $plugin_page ] ) ) {
					return;
				}

				$current_moved_page = $moved[ $plugin_page ];

				$original_pagenow = $pagenow;

				/*
				 * Temporarily modify the $pagenow global to set it to the original parent page slug so that the other globals
				 * will be set as if the plugin page was still in its original location. It cannot be set to the original
				 * parent file as that would not be registered with the correct page hook.
				 * Afterwards, hook into the load process to reset $pagenow to the original parent page file.
				 */
				if ( 'admin.php' === $pagenow && 'admin.php' === $current_moved_page['new_parent_file'] ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$pagenow = $moved[ $plugin_page ]['old_parent'];

					add_action(
						'load-' . get_plugin_page_hook( $plugin_page, $pagenow ),
						function () {
							global $pagenow;

							// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							$pagenow = 'admin.php';
						}
					);
				}

				/*
				 * Sometimes plugins link to specific URLs using hashes, e.g. for React apps.
				 * We can't get the URL hash on the server-side.
				 * So we should try to identify the relevant hash child by assumption.
				 */
				if ( $current_moved_page['hash_children'] ) {
					$found_hash_child = null;
					foreach ( $current_moved_page['hash_children'] as $submenu_page_slug ) {
						if ( isset( $moved[ $submenu_page_slug ] ) && $original_pagenow === $moved[ $submenu_page_slug ]['new_parent_file'] ) {
							$found_hash_child = $moved[ $submenu_page_slug ];
							break;
						}
					}

					if ( ! $found_hash_child ) {
						return;
					}

					add_filter(
						'parent_file',
						function () use ( $found_hash_child ) {
							return $found_hash_child['new_parent'];
						}
					);
					add_filter(
						'submenu_file',
						function () use ( $submenu_page_slug ) {
							return $submenu_page_slug;
						}
					);
					add_filter(
						'admin_title',
						function ( $admin_title, $title ) use ( $submenu_page_slug, $found_hash_child ) {
							if ( ! $title ) {
								global $submenu;
								if ( isset( $submenu[ $found_hash_child['new_parent'] ] ) ) {
									foreach ( $submenu[ $found_hash_child['new_parent'] ] as $submenu_item ) {
										if ( $submenu_page_slug === $submenu_item[2] ) {
											$title = isset( $submenu_item[3] ) ? $submenu_item[3] : $submenu_item[0];
											return $title . $admin_title;
										}
									}
								}
							}
							return $admin_title;
						},
						10,
						2
					);
				}
			}
		);
	},
	PHP_INT_MAX
);
