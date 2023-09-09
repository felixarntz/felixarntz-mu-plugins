<?php
/**
 * Plugin Name: Bulk Edit Defer Term Counting
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Defers term counting when bulk editing to avoid slow queries for each post updated.
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
	'load-edit.php',
	static function () {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['bulk_edit'] ) ) {
			wp_defer_term_counting( true );
			add_action(
				'shutdown',
				static function () {
					wp_defer_term_counting( false );
				}
			);
		}
	}
);
