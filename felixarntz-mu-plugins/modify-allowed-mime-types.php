<?php
/**
 * Plugin Name: Modify Allowed MIME Types
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Modifies the MIME types allowed for upload in the media library.
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

add_filter(
	'upload_mimes',
	static function ( $mime_types ) {
		$config = Shared\Config::instance();

		$allowed    = array_filter( $config->get( 'allowed_mime_types', array() ) );
		$disallowed = array_filter( $config->get( 'disallowed_mime_types', array() ) );

		// Bail without changes if no customizations are provided.
		if ( ! $allowed && ! $disallowed ) {
			return $mime_types;
		}

		// Restructure the MIME types array to be keyed by type.
		$type_map = array();
		foreach ( $mime_types as $regex => $mime_type ) {
			list( $type, $subtype ) = explode( '/', $mime_type, 2 );
			if ( ! isset( $type_map[ $type ] ) ) {
				$type_map[ $type ] = array();
			}
			$type_map[ $type ][ $mime_type ] = $regex;
		}

		if ( $allowed ) {
			$new_type_map = array();
			foreach ( $allowed as $allowed_mime ) {
				// If a full MIME type is provided, add it to the new type map.
				if ( str_contains( $allowed_mime, '/' ) ) {
					list( $type, $subtype ) = explode( '/', $allowed_mime, 2 );
					if ( isset( $type_map[ $type ][ $allowed_mime ] ) ) {
						if ( ! isset( $new_type_map[ $type ] ) ) {
							$new_type_map[ $type ] = array();
						}
						$new_type_map[ $type ][ $allowed_mime ] = $type_map[ $type ][ $allowed_mime ];
					}
					continue;
				}

				// Otherwise, add all subtypes of the type to the new type map.
				if ( isset( $type_map[ $allowed_mime ] ) ) {
					$new_type_map[ $allowed_mime ] = $type_map[ $allowed_mime ];
				}
			}

			$type_map = $new_type_map;
		}

		if ( $disallowed ) {
			foreach ( $disallowed as $disallowed_mime ) {
				// If a full MIME type is provided, remove it from the type map.
				if ( str_contains( $disallowed_mime, '/' ) ) {
					list( $type, $subtype ) = explode( '/', $disallowed_mime, 2 );
					unset( $type_map[ $type ][ $disallowed_mime ] );
					continue;
				}

				// Otherwise, remove all subtypes of the type from the type map.
				unset( $type_map[ $disallowed_mime ] );
			}
		}

		// Rebuild the MIME types array.
		$mime_types = array();
		foreach ( $type_map as $type => $subtypes ) {
			foreach ( $subtypes as $mime_type => $regex ) {
				$mime_types[ $regex ] = $mime_type;
			}
		}

		return $mime_types;
	}
);
