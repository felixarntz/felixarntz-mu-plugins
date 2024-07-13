<?php
/**
 * Plugin Name: Add Edit Layout Capability
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Adds a dedicated capability for editing layout in the block editor.
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

_deprecated_file( 'add-edit-layout-capability.php', 'felixarntz-mu-plugins 1.2.0', 'add-block-editor-capabilities.php' );
require_once __DIR__ . '/add-block-editor-capabilities.php';
