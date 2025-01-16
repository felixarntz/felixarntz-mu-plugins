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

			$new_hookname = get_plugin_page_hookname( $hook_submenu_slug, $new_menu_slug );

			// Copy old page hook so that links to the admin page work correctly.
			if ( isset( $wp_filter[ $old_hookname ] ) ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$wp_filter[ $new_hookname ] = $wp_filter[ $old_hookname ];
			}

			return array(
				'identifier'   => $submenu_page[2],
				'old_parent'   => $menu_page[2],
				'new_parent'   => $new_menu_slug,
				'old_hookname' => $old_hookname,
				'new_hookname' => $new_hookname,
			);
		};

		$jetpack_site_id = false;
		if ( class_exists( 'Automattic\Jetpack\Connection\Manager' ) ) {
			$jetpack_site_id = \Automattic\Jetpack\Connection\Manager::get_site_id( true );
		}
		if ( ! $jetpack_site_id ) {
			$jetpack_site_id = rtrim(
				str_replace(
					'/',
					'::',
					preg_replace( '#^.*?://#', '', home_url() )
				),
				':'
			);
		}
		$jetpack_subscribers_old = esc_url(
			'https://jetpack.com/redirect/?source=jetpack-menu-calypso-subscribers&site=' . $jetpack_site_id
		);
		$jetpack_subscribers_new = esc_url(
			'https://jetpack.com/redirect/?source=jetpack-menu-jetpack-manage-subscribers&site=' . $jetpack_site_id
		);

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
				$jetpack_subscribers_old  => 'insights',
				$jetpack_subscribers_new  => 'insights',
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
				$submenu_slug = $submenu_page[2];
				if ( str_starts_with( $submenu_slug, admin_url( '/' ) ) && str_contains( $submenu_slug, 'page=' ) ) {
					$submenu_slug = preg_match( '/(\?|&)page=([^&]+)/', $submenu_slug, $matches ) ? $matches[2] : $submenu_slug;
				}
				if ( isset( $move_plugin_menus[ $submenu_slug ] ) ) {
					// Do not make any change if no target set or explicitly forced to keep.
					if ( empty( $move_plugin_menus[ $submenu_slug ] ) || 'keep' === $move_plugin_menus[ $submenu_slug ] ) {
						continue;
					}

					// Simply hide the submenu page if target set to 'hide'.
					if ( 'hide' === $move_plugin_menus[ $submenu_slug ] ) {
						$admin_menu->remove_submenu_page( $menu_page[2], $submenu_page[2] );
						continue;
					}

					// Otherwise, attempt to move to the target menu set.
					$migration_data = $migrate_item( $submenu_page, $menu_page, $move_plugin_menus[ $submenu_slug ] );
					if ( $migration_data ) {
						$moved[ $submenu_slug ]                    = $migration_data;
						$moved[ $submenu_slug ]['new_parent_file'] = str_ends_with( $migration_data['new_parent'], '.php' ) ? $migration_data['new_parent'] : 'admin.php';
						$moved[ $submenu_slug ]['hash_children']   = array();
						if ( str_contains( $submenu_slug, '#' ) ) {
							$unhashed_slug = explode( '#', $submenu_slug )[0];
							if ( isset( $moved[ $unhashed_slug ] ) ) {
								if ( 'admin.php' === $moved[ $submenu_slug ]['new_parent_file'] ) {
									$moved[ $unhashed_slug ]['new_parent']      = $moved[ $submenu_slug ]['new_parent'];
									$moved[ $unhashed_slug ]['new_parent_file'] = $moved[ $submenu_slug ]['new_parent_file'];
								}
								$moved[ $unhashed_slug ]['hash_children'][] = $submenu_slug;
								continue;
							}
							$moved[ $unhashed_slug ]                  = $moved[ $submenu_slug ];
							$moved[ $unhashed_slug ]['hash_children'] = array( $submenu_slug );
						}
					}
				}
			}
		}

		// Jetpack-specific fix, in case its menus are moved.
		add_action(
			'admin_footer',
			static function () {
				$js = 'const hiddenElem = document.createElement( "div" );
hiddenElem.id = "toplevel_page_jetpack";
hiddenElem.style.display = "none";
hiddenElem.innerHTML = "<div class=\'wp-submenu\'></div>";
document.body.appendChild( hiddenElem );';
				wp_add_inline_script( 'react-plugin', $js, 'before' );
				wp_add_inline_script( 'react-plugin', 'window.wpNavMenuClassChange = () => {};' );
				wp_add_inline_script( 'react-plugin', 'document.body.removeChild( document.getElementById( "toplevel_page_jetpack" ) )' );
			}
		);

		add_action(
			'admin_init',
			function () use ( $moved, $admin_menu ) {
				global $pagenow, $plugin_page, $title;

				if ( ! isset( $plugin_page ) ) {
					return;
				}

				// Prevent PHP warnings about `$title` being null.
				if ( ! is_string( $title ) ) {
					$page_item = $admin_menu->get_menu_page( $plugin_page );
					if ( ! $page_item ) {
						$page_item = $admin_menu->get_submenu_page( '', $plugin_page );
					}
					$title = $page_item ? $page_item[0] : ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				}

				if ( ! isset( $moved[ $plugin_page ] ) ) {
					return;
				}

				$current_moved_page = $moved[ $plugin_page ];
				$real_page_slug     = $plugin_page;

				// If there are hashed children, try to find a more specific match within those.
				if ( $current_moved_page['hash_children'] ) {
					foreach ( $current_moved_page['hash_children'] as $submenu_page_slug ) {
						if ( isset( $moved[ $submenu_page_slug ] ) && $pagenow === $moved[ $submenu_page_slug ]['new_parent_file'] ) {
							$current_moved_page = $moved[ $submenu_page_slug ];
							$real_page_slug     = $submenu_page_slug;
							break;
						}
					}
				}

				// Override the hook suffix global to make sure relevant hooks added based on the original location still fire.
				if ( get_plugin_page_hookname( $plugin_page, $pagenow ) === $current_moved_page['new_hookname'] ) {
					add_action(
						'load-' . $current_moved_page['new_hookname'],
						function () use ( $current_moved_page ) {
							global $hook_suffix;

							// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							$hook_suffix = $current_moved_page['old_hookname'];
						}
					);
				}

				// Override further variables if this is a hashed child page.
				if ( $real_page_slug !== $plugin_page ) {
					// Set $title global to prevent it from being null, which can cause a PHP notice.
					$submenu_page = $admin_menu->get_submenu_page( $current_moved_page['new_parent'], $current_moved_page['identifier'] );
					if ( $submenu_page ) {
						// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						$GLOBALS['title'] = isset( $submenu_page[3] ) ? $submenu_page[3] : $submenu_page[0];
					}

					// Override parent and submenu file globals.
					add_filter(
						'parent_file',
						function () use ( $current_moved_page ) {
							return $current_moved_page['new_parent'];
						}
					);
					add_filter(
						'submenu_file',
						function () use ( $real_page_slug ) {
							return $real_page_slug;
						}
					);
				}
			}
		);
	},
	PHP_INT_MAX
);
