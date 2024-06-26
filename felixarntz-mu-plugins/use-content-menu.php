<?php
/**
 * Plugin Name: Use Content Menu
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Moves all post type admin menus into a single Content menu.
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
		global $submenu;

		$admin_menu = Shared\Admin_Menu::instance();

		$posts_menu = $admin_menu->get_menu_page( 'edit.php' );
		if ( ! $posts_menu ) {
			return;
		}

		$menu_tmpl    = 'edit.php?post_type=%s';
		$add_new_tmpl = 'post-new.php?post_type=%s';
		$post_types   = array_merge(
			array( 'page' ),
			get_post_types(
				array(
					'show_ui'      => true,
					'_builtin'     => false,
					'show_in_menu' => true,
				)
			)
		);

		// Check whether there is more than 1 relevant menu to migrate.
		$eligible_post_types = array_filter(
			$post_types,
			static function ( $post_type ) use ( $admin_menu, $menu_tmpl ) {
				$menu_file = sprintf( $menu_tmpl, $post_type );
				return $admin_menu->get_menu_page( $menu_file ) ? true : false;
			}
		);
		if ( count( $eligible_post_types ) <= 1 ) {
			return;
		}

		// Change the Posts menu to a general content menu, and use title "Posts" instead of "All Posts" for submenu.
		$admin_menu->update_submenu_page_menu_title( 'edit.php', 'edit.php', $posts_menu[0] );
		$admin_menu->update_menu_page_menu_title( 'edit.php', __( 'Content', 'default' ) );

		$taxonomies = get_taxonomies(
			array(
				'show_ui'      => true,
				'show_in_menu' => true,
			),
			'objects'
		);
		foreach ( $eligible_post_types as $post_type ) {
			$menu_file    = sprintf( $menu_tmpl, $post_type );
			$add_new_file = sprintf( $add_new_tmpl, $post_type );

			$post_type_menu = $admin_menu->get_menu_page( $menu_file );
			if ( ! $post_type_menu ) {
				continue;
			}

			$post_type_taxonomies = array_filter(
				$taxonomies,
				function ( $taxonomy ) use ( $post_type ) {
					return in_array( $post_type, (array) $taxonomy->object_type, true );
				}
			);

			$expected_num_pages = count( $post_type_taxonomies ) + 1;
			if ( $admin_menu->get_submenu_page( $menu_file, $add_new_file ) ) {
				$expected_num_pages++;
			}

			// Skip if there are other submenu pages in the post type's menu.
			if ( $admin_menu->get_submenu_page_count( $menu_file ) > $expected_num_pages ) {
				continue;
			}

			// Remove Add New page if present.
			$admin_menu->remove_submenu_page( $menu_file, $add_new_file );

			// Rename submenu item to use the overall menu's title (e.g. "Pages" instead of "All Pages").
			$admin_menu->update_submenu_page_menu_title( $menu_file, $menu_file, $post_type_menu[0] );

			// Move all submenu pages to the new Content menu.
			$submenu_page = $admin_menu->get_first_submenu_page( $menu_file );
			while ( $submenu_page ) {
				if ( $admin_menu->move_submenu_page( $menu_file, $submenu_page[2], 'edit.php' ) ) {
					$submenu_page = $admin_menu->get_first_submenu_page( $menu_file );
				} else {
					$submenu_page = array();
				}
			}
		}

		// Override the current parent menu file to be the new "Content" menu where relevant.
		add_filter(
			'parent_file',
			static function ( $parent_file ) {
				global $post_type, $post_type_object;

				if ( ! isset( $post_type ) ) {
					return $parent_file;
				}

				if ( isset( $post_type_object ) && $post_type_object->show_in_menu && true !== $post_type_object->show_in_menu ) {
					$orig_parent_file = $post_type_object->show_in_menu;
				} else {
					$orig_parent_file = "edit.php?post_type=$post_type";
				}

				if ( $parent_file === $orig_parent_file ) {
					return 'edit.php';
				}

				return $parent_file;
			}
		);

		/**
		 * Depending on the configuration, either sort the Content submenu items so that all post types come first,
		 * before any taxonomies, or alternatively keep the original order and visually indent the taxonomy entries.
		 */
		$config = Shared\Config::instance();
		if ( ! $config->get( 'indent_content_menu_taxonomies', false ) ) {
			$taxonomy_pages = array();
			foreach ( $submenu['edit.php'] as $index => $submenu_page ) {
				if ( str_starts_with( $submenu_page[2], 'edit-tags.php' ) ) {
					$taxonomy_pages[] = $submenu_page;
					unset( $submenu['edit.php'][ $index ] );
				}
			}
			foreach ( $taxonomy_pages as $taxonomy_page ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$submenu['edit.php'][] = $taxonomy_page;
			}
			return;
		}

		add_action(
			'admin_head',
			static function () {
				?>
				<style type="text/css">
					.wp-submenu a[href^="edit-tags.php"] {
						padding-left: 20px !important;
					}
				</style>
				<?php
			}
		);
	},
	PHP_INT_MAX
);
