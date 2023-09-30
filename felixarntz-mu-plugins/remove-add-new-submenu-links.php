<?php
/**
 * Plugin Name: Remove Add New Submenu Links
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Removes all the Add New submenu items in the admin.
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
		$admin_menu = Shared\Admin_Menu::instance();

		$add_new_items = array(
			'edit.php'    => 'post-new.php',
			'upload.php'  => 'media-new.php',
			'plugins.php' => 'plugin-install.php',
			'users.php'   => 'user-new.php',
		);
		foreach ( $add_new_items as $menu_file => $submenu_file ) {
			$admin_menu->remove_submenu_page( $menu_file, $submenu_file );
		}

		$menu_tmpl    = 'edit.php?post_type=%s';
		$submenu_tmpl = 'post-new.php?post_type=%s';
		$post_types   = get_post_types(
			array(
				'show_ui'      => true,
				'show_in_menu' => true,
			)
		);
		foreach ( $post_types as $post_type ) {
			$menu_file    = sprintf( $menu_tmpl, $post_type );
			$submenu_file = sprintf( $submenu_tmpl, $post_type );
			$admin_menu->remove_submenu_page( $menu_file, $submenu_file );
		}
	},
	9999
);
