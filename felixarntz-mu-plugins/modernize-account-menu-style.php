<?php
/**
 * Plugin Name: Modernize Account Menu Style
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Modifies the styling of the account menu in the admin bar to display a larger circled avatar image.
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

/**
 * Prints additional styles for the account menu in the admin bar.
 */
function print_extra_admin_bar_account_menu_styles() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}

	?>
	<style type="text/css">
		#wpadminbar #wp-admin-bar-my-account.with-avatar #wp-admin-bar-user-actions > li {
			margin-left: 16px;
		}

		#wp-admin-bar-user-info {
			display: block;
			padding-bottom: 10px !important;
			border-bottom: 1px solid #c3c4c7 !important;
		}

		#wp-admin-bar-user-info .avatar {
			position: static;
			display: block;
			margin: 0 auto;
			width: 128px;
			height: auto;
			border-radius: 50%;
		}

		#wpadminbar #wp-admin-bar-user-info .display-name,
		#wpadminbar #wp-admin-bar-user-info .username {
			display: block;
			text-align: center;
			height: auto;
		}
	</style>
	<?php
}

add_action( 'wp_head', __NAMESPACE__ . '\\print_extra_admin_bar_account_menu_styles' );
add_action( 'admin_head', __NAMESPACE__ . '\\print_extra_admin_bar_account_menu_styles' );
