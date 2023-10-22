<?php
/**
 * Loader for shared code.
 *
 * @package felixarntz-mu-plugins
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '/class-config.php';
require_once __DIR__ . '/class-file-loader.php';
require_once __DIR__ . '/class-admin-menu.php';
