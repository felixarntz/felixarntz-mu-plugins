<?php
/**
 * Plugin Name: Felix Arntz MU Plugins
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: My collection of MU plugins in individual files within a subdirectory.
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

require_once __DIR__ . '/felixarntz-mu-plugins/shared/loader.php';

/**
 * Loads the MU plugin files from the subdirectory.
 */
function felixarntz_mu_plugins_load() {
	// Optional allowlist of slugs (file names without PHP extension).
	$allowlist = array();

	// If allowlist is used, load only those files, otherwise load all files.
	if ( $allowlist ) {
		$files = array_map(
			static function ( $slug ) {
				return __DIR__ . '/felixarntz-mu-plugins/' . $slug . '.php';
			}
		);
	} else {
		$files = glob( __DIR__ . '/felixarntz-mu-plugins/*.php' );
	}

	foreach ( $files as $file ) {
		require_once $file;
	}
}

felixarntz_mu_plugins_load();
