<?php
/**
 * Plugin Name: Disable Block Editor Full Screen Mode
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Disables the block editor's full screen mode by default.
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

add_action(
	'enqueue_block_editor_assets',
	static function () {
		$script = "window.onload = function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } }";
		wp_add_inline_script( 'wp-blocks', $script );
	}
);
