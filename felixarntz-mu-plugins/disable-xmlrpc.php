<?php
/**
 * Plugin Name: Disable XMLRPC
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables XML-RPC access to the site.
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

add_filter( 'xmlrpc_enabled', '__return_false' );
