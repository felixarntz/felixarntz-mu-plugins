<?php
/**
 * Plugin Name: Modify Rest Root
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Modifies the REST API root to a different one, by default using api.
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

add_filter(
	'rest_url_prefix',
	static function () {
		$config = Shared\Config::instance();
		return $config->get( 'rest_root', 'api' );
	}
);

add_filter(
	'subdirectory_reserved_names',
	static function ( $names ) {
		$config  = Shared\Config::instance();
		$names[] = $config->get( 'rest_root', 'api' );
		return $names;
	}
);
