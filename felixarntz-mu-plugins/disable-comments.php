<?php
/**
 * Plugin Name: Disable Comments
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables comments.
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

// Force comment status to closed.
add_filter( 'comments_open', '__return_false' );
add_filter(
	'pre_option_default_comment_status',
	static function () {
		return 'closed';
	}
);

// Hide UI to control comments.
add_action(
	'init',
	static function () {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
	},
	100
);
add_action(
	'admin_init',
	static function () {
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	}
);
add_action(
	'admin_head',
	static function () {
		global $pagenow;

		if ( 'options-discussion.php' !== $pagenow ) {
			return;
		}

		?>
<style type="text/css">
	label[for="default_comment_status"],
	label[for="default_comment_status"] + br {
		display: none;
	}
</style>
		<?php
	}
);

// Remove comments menu items and redirect direct access.
add_action(
	'admin_menu',
	static function () {
		remove_menu_page( 'edit-comments.php' );

		// If pingbacks are also disabled, remove the entire Discussion settings page.
		if ( has_filter( 'pings_open', '__return_false' ) ) {
			remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		}
	}
);
add_action(
	'admin_init',
	static function () {
		global $pagenow;

		$screens_to_redirect = array( 'edit-comments.php' => true );

		// If pingbacks are also disabled, prevent access to the entire Discussion settings page.
		if ( has_filter( 'pings_open', '__return_false' ) ) {
			$screens_to_redirect['options-discussion.php'] = true;
		}

		if ( isset( $screens_to_redirect[ $pagenow ] ) ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
	},
	1
);

