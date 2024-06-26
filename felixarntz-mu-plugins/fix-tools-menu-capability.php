<?php
/**
 * Plugin Name: Fix Tools Menu Capability
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Ensures that the Tools menu is only shown if the user has the capabilities to do something with it.
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
		// Bail if additional cards may be present under "Available Tools".
		if ( has_action( 'tool_box' ) ) {
			return;
		}

		$admin_menu = Shared\Admin_Menu::instance();
		if ( $admin_menu->update_menu_page_cap( 'tools.php', 'import' ) ) {
			$admin_menu->update_submenu_page_cap( 'tools.php', 'tools.php', 'import' );
		}
	},
	PHP_INT_MAX
);
