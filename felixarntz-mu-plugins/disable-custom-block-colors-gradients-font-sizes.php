<?php
/**
 * Plugin Name: Disable Custom Block Colors Gradients Font Sizes
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables custom colors, custom gradients, custom font sizes etc. for the block editor to enforce a uniform style.
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
	'block_editor_settings_all',
	static function ( $editor_settings ) {
		$editor_settings['disableCustomColors']       = true;
		$editor_settings['disableCustomGradients']    = true;
		$editor_settings['disableCustomFontSizes']    = true;
		$editor_settings['disableCustomSpacingSizes'] = true;

		return $editor_settings;
	}
);
