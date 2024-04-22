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
			'base_color'      => $config->get( 'admin_color_scheme_base_color', '#1d2327' ),
			'icon_color'      => $config->get( 'admin_color_scheme_icon_color', '#a7aaad' ),
			'text_color'      => $config->get( 'admin_color_scheme_text_color', '#fff' ),
			'highlight_color' => $config->get( 'admin_color_scheme_highlight_color', '#2271b1' ),
			'accent_color'    => $config->get( 'admin_color_scheme_accent_color', '#d63638' ),
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
				Shared\Color_Utils::to_css_string( $colors['accent_color'] ),
			),
			array(
				'base'    => $colors['icon_color'],
				'focus'   => $colors['text_color'],
				'current' => $colors['text_color'],
			)
		);

		/*
		 * If the color scheme is currently in use, make sure to include the corresponding CSS variables.
		 * Also include them when on the profile page, to immediately reflect the correct colors if the user changes
		 * the color scheme.
		 */
		$current_color_scheme = get_user_option( 'admin_color' );
		if ( 'brand' === $current_color_scheme || isset( $GLOBALS['pagenow'] ) && 'profile.php' === $GLOBALS['pagenow'] ) {
			$inline_css = ':root {';
			foreach ( $colors as $color_id => $color_value ) {
				$inline_css .= ' --brand-color-scheme-' . str_replace( '_', '-', $color_id ) . ':';
				$inline_css .= ' ' . esc_attr( Shared\Color_Utils::to_css_string( $color_value ) ) . ';';
			}
			$inline_css .= ' }';
			wp_add_inline_style( 'colors', $inline_css );
		}

		// If enforced, hook in relevant logic.
		if ( $config->get( 'admin_color_scheme_enforced', false ) ) {
			// Remove default color schemes, unless previously explicitly selected.
			$default_color_schemes = array(
				'fresh',
				'light',
				'blue',
				'midnight',
				'sunrise',
				'ectoplasm',
				'ocean',
				'coffee',
				'modern',
			);
			foreach ( $default_color_schemes as $color_scheme ) {
				if ( $color_scheme !== $current_color_scheme ) {
					unset( $GLOBALS['_wp_admin_css_colors'][ $color_scheme ] );
				}
			}

			// Override the default color scheme.
			add_filter(
				'get_user_option_admin_color',
				static function ( $value ) {
					return $value ? $value : 'brand';
				}
			);

			// Add hidden input for default color scheme to ensure the custom scheme remains selected.
			add_action(
				'personal_options',
				static function () {
					if ( count( $GLOBALS['_wp_admin_css_colors'] ) <= 1 || ! has_action( 'admin_color_scheme_picker' ) ) {
						echo '<input type="hidden" name="admin_color" value="brand" />';
					}
				}
			);
		}
	}
);
