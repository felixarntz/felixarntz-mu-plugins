<?php
/**
 * Plugin Name: Disable Pingbacks
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables pingbacks and trackbacks.
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

// Force ping status to closed.
add_filter( 'pings_open', '__return_false' );
add_filter(
	'pre_option_default_ping_status',
	static function () {
		return 'closed';
	}
);

// Hide UI to control ping status.
add_action(
	'admin_head',
	static function () {
		global $pagenow;

		if ( 'options-discussion.php' !== $pagenow ) {
			return;
		}

		?>
<style type="text/css">
	label[for="default_ping_status"],
	label[for="default_ping_status"] + br {
		display: none;
	}
</style>
		<?php
	}
);

// Disable pingbacks in XML-RPC.
add_filter(
	'xmlrpc_methods',
	static function ( $methods ) {
		unset( $methods['pingback.ping'] );
		return $methods;
	}
);
add_action(
	'xmlrpc_call',
	static function ( $action ) {
		if ( 'pingback.ping' === $action ) {
			wp_die( 'Pingbacks are not supported', 'Not Allowed!', array( 'response' => 403 ) );
		}
	}
);

// Remove X-Pingback response header.
add_filter(
	'wp_headers',
	static function ( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}
);

// Remove 'trackbacks' support from all post types.
add_action(
	'init',
	static function () {
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'trackbacks' ) ) {
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	},
	100
);

// Remove trackback rewrite rules.
add_filter(
	'rewrite_rules_array',
	static function ( $rules ) {
		foreach ( array_keys( $rules ) as $rule ) {
			if ( preg_match( '/trackback\/\?\$$/i', $rule ) ) {
				unset( $rules[ $rule ] );
			}
		}
		return $rules;
	}
);
