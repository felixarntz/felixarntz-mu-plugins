<?php
/**
 * Plugin Name: Add Client Role
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Adds a role for clients with additional capabilities than editors, but not quite admin.
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
	'init',
	static function () {
		// The client role inherits all capabilities from the editor role.
		$editor_role = get_role( 'editor' );
		if ( ! $editor_role ) {
			return;
		}

		$config = Shared\Config::instance();

		$display_name    = $config->get( 'client_role_display_name', '' );
		$additional_caps = $config->get( 'client_role_additional_caps', array() );
		if ( ! $display_name ) {
			$display_name = 'Client';
		}
		if ( ! $additional_caps ) {
			$additional_caps = array(
				'update_core',
				'update_plugins',
				'update_themes',
			);
		}

		$capabilities = $editor_role->capabilities;

		// If an indexed array, transform it to a capabilities map with each capability granted.
		if ( isset( $additional_caps[0] ) ) {
			$additional_caps = array_fill_keys( $additional_caps, true );
		}
		foreach ( $additional_caps as $cap => $grant ) {
			// Do not allow removing capabilities.
			if ( isset( $capabilities[ $cap ] ) && $capabilities[ $cap ] ) {
				continue;
			}
			$capabilities[ $cap ] = $grant;
		}

		$roles = array(
			'felixarntz_client' => array(
				'display_name' => $display_name,
				'capabilities' => $capabilities,
			),
		);

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		$roles_hash      = md5( serialize( $roles ) );
		$roles_installed = get_option( 'felixarntz_roles_installed' );
		if ( $roles_installed !== $roles_hash ) {
			foreach ( $roles as $role => $data ) {
				add_role( $role, $data['display_name'], $data['capabilities'] );
			}
			update_option( 'felixarntz_roles_installed', $roles_hash );
		}
	}
);
