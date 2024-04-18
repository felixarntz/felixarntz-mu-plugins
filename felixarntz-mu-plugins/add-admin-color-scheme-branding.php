<?php
/**
 * Plugin Name: Add Admin Color Scheme Branding
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Adds an admin color scheme reflecting the specific brand colors.
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
	'admin_init',
	static function () {
		$config = Shared\Config::instance();

		// Required colors.
		$colors = array(
			'base_color'      => $config->get( 'admin_color_scheme_base_color', '#52accc' ),
			'icon_color'      => $config->get( 'admin_color_scheme_icon_color', '#e5f8ff' ),
			'text_color'      => $config->get( 'admin_color_scheme_text_color', '#fff' ),
			'highlight_color' => $config->get( 'admin_color_scheme_highlight_color', '#096484' ),
			'accent_color'    => $config->get( 'admin_color_scheme_accent_color', '#e1a948' ),
			'link_color'      => $config->get( 'admin_color_scheme_link_color', '#0073aa' ),
		);

		// Optional colors.
		$colors['secondary_base_color'] = $config->get( 'admin_color_scheme_secondary_base_color', '' );
		$colors['secondary_text_color'] = $config->get( 'admin_color_scheme_secondary_text_color', '' );
		if ( ! $colors['secondary_base_color'] ) {
			$colors['secondary_base_color'] = Shared\Color_Utils::darken_hsl(
				Shared\Color_Utils::hex_to_hsl( $colors['base_color'] ),
				7
			);
		}
		if ( ! $colors['secondary_text_color'] ) {
			$colors['secondary_text_color'] = Shared\Color_Utils::darken_hsl(
				Shared\Color_Utils::hex_to_hsl( $colors['text_color'] ),
				7
			);
		}

		// Computed colors.
		$colors['base_color_alt']           = Shared\Color_Utils::lighten_hsl(
			Shared\Color_Utils::hex_to_hsl( $colors['base_color'] ),
			7
		);
		$colors['highlight_color_alt']      = Shared\Color_Utils::darken_hsl(
			Shared\Color_Utils::hex_to_hsl( $colors['highlight_color'] ),
			10
		);
		$colors['accent_color_alt']         = Shared\Color_Utils::lighten_hsl(
			Shared\Color_Utils::hex_to_hsl( $colors['accent_color'] ),
			10
		);
		$colors['link_color_alt']           = Shared\Color_Utils::lighten_hsl(
			Shared\Color_Utils::hex_to_hsl( $colors['link_color'] ),
			10
		);
		$colors['secondary_base_color_alt'] = $colors['secondary_base_color'];
		if ( ! is_array( $colors['secondary_base_color_alt'] ) ) {
			$colors['secondary_base_color_alt'] = Shared\Color_Utils::hex_to_hsl( $colors['secondary_base_color_alt'] );
		}
		$colors['secondary_base_color_alt'] = Shared\Color_Utils::desaturate_hsl( Shared\Color_Utils::lighten_hsl( $colors['secondary_base_color_alt'], 7 ), 7 );

		$suffix = is_rtl() ? '-rtl' : '';

		wp_admin_css_color(
			'brand',
			_x( 'Brand', 'admin color scheme', 'felixarntz-mu-plugins' ),
			plugin_dir_url( __FILE__ ) . "admin-css-color-scheme/colors$suffix.css",
			array(
				Shared\Color_Utils::to_css_string( $colors['highlight_color'] ),
				Shared\Color_Utils::to_css_string( $colors['secondary_base_color'] ),
				Shared\Color_Utils::to_css_string( $colors['base_color'] ),
				Shared\Color_Utils::to_css_string( $colors['secondary_base_color_alt'] ),
			),
			array(
				'base'    => $colors['icon_color'],
				'focus'   => $colors['text_color'],
				'current' => $colors['text_color'],
			)
		);

		// If the color scheme is currently in use, make sure to include the corresponding CSS variables.
		$color_scheme = get_user_option( 'admin_color' );
		if ( 'brand' === $color_scheme ) {
			$inline_css = ':root {';
			foreach ( $colors as $color_id => $color_value ) {
				$inline_css .= ' --brand-color-scheme-' . str_replace( '_', '-', $color_id ) . ':';
				$inline_css .= ' ' . esc_attr( Shared\Color_Utils::to_css_string( $color_value ) ) . ';';
			}
			$inline_css .= ' }';
			wp_add_inline_style( 'colors', $inline_css );
		}
	}
);
