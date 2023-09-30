<?php
/**
 * Plugin Name: Make Site Private
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Makes the entire site private so that only logged-in users can see the content.
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

// Redirect regular non logged-in traffic.
add_action(
	'wp',
	static function () {
		// Don't redirect if the user is logged in - obviously.
		if ( is_user_logged_in() ) {
			return;
		}

		// Don't redirect REST request, instead use authentication error.
		if ( defined( 'REST_REQUEST' ) ) {
			return;
		}

		// Don't redirect any of these screens.
		$exclusions = array(
			'wp-login.php'     => true,
			'wp-activate.php'  => true,
			'wp-signup.php'    => true,
			'wp-cron.php'      => true,
			'wp-trackback.php' => true,
			'xmlrpc.php'       => true,
		);
		if ( isset( $exclusions[ basename( $_SERVER['PHP_SELF'] ) ] ) ) {
			return;
		}

		auth_redirect();
	}
);

// Force REST API error if not logged in.
add_filter(
	'rest_authentication_errors',
	static function ( $result ) {
		// If there is an error already, pass it through.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! is_user_logged_in() ) {
			return new \WP_Error(
				'rest_not_logged_in',
				__( 'You need to be logged in to access this content.', 'felixarntz-mu-plugins' ),
				array( 'status' => 401 )
			);
		}

		return $result;
	}
);

// Show login error message based on redirect.
add_action(
	'init',
	static function () {
		global $error;

		// phpcs:ignore WordPress.Security.NonceVerification
		if ( 'wp-login.php' !== basename( $_SERVER['PHP_SELF'] ) || ! empty( $_POST ) || ( ! empty( $_GET ) && empty( $_GET['redirect_to'] ) ) ) {
			return;
		}

		// If there is no redirect or it is pointing to the admin, there is no need to show the custom error.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$redirect = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
		if ( ! $redirect || str_starts_with( $redirect, admin_url() ) ) {
			return;
		}

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$error = __( 'You need to be logged in to access this content.', 'felixarntz-mu-plugins' );
	}
);

// Disable indexing.
add_action( 'pre_option_blog_public', '__return_zero' );
