<?php
/**
 * Plugin Name: Use Ambiguous Login Error
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Modifies the error messages for a failed login attempt to be more ambiguous.
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

add_filter(
	'login_errors',
	static function ( $error ) {
		global $errors;

		if ( ! is_wp_error( $errors ) ) {
			return $error;
		}

		$error_codes = array_intersect(
			$errors->get_error_codes(),
			array(
				'invalid_username',
				'invalid_email',
				'incorrect_password',
				'invalidcombo',
			)
		);
		if ( $error_codes ) {
			$error  = '<strong>' . esc_html__( 'Error:', 'felixarntz-mu-plugins' ) . '<strong> ';
			$error .= esc_html__( 'The username/email address or password is incorrect. Please try again.', 'felixarntz-mu-plugins' );
		}

		return $error;
	}
);
