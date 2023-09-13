<?php
/**
 * Plugin Name: Add Login Branding
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Adds site specific branding to the login page.
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

/**
 * Converts a HEX value to RGB.
 *
 * Mostly copied from Twenty Sixteen theme.
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ) . substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ) . substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ) . substr( $color, 2, 1 ) );
	} elseif ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array(
		'r' => $r,
		'g' => $g,
		'b' => $b,
	);
}

// Displays custom branding as configured, or using the site icon if set.
add_action(
	'login_head',
	static function () {
		$config = Shared\Config::instance();

		$highlight_color       = $config->get( 'login_highlight_color', '' );
		$highlight_color_hover = $config->get( 'login_highlight_color_hover', '' );
		$header_image_url      = $config->get( 'login_header_image_url', '' );
		$header_image_size     = $config->get( 'login_header_image_size', '' );

		// Use site icon if no header image is set.
		if ( ! $header_image_url || ! $header_image_size ) {
			$header_image_url  = get_site_icon_url( 192 );
			$header_image_size = 192;
		}

		if ( $highlight_color && $highlight_color_hover ) {
			$rgb                    = hex2rgb( $highlight_color );
			$highlight_color_shadow = 'rgba(' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ', 0.8)';

			?>
			<style type="text/css">
				a {
					color: <?php echo esc_attr( $highlight_color ); ?>;
				}

				a:hover,
				a:active {
					color: <?php echo esc_attr( $highlight_color_hover ); ?>;
				}

				.login #nav a:hover,
				.login #backtoblog a:hover,
				.login h1 a:hover {
					color: <?php echo esc_attr( $highlight_color_hover ); ?>;
				}

				input[type="text"]:focus,
				input[type="password"]:focus,
				input[type="color"]:focus,
				input[type="date"]:focus,
				input[type="datetime"]:focus,
				input[type="datetime-local"]:focus,
				input[type="email"]:focus,
				input[type="month"]:focus,
				input[type="number"]:focus,
				input[type="password"]:focus,
				input[type="search"]:focus,
				input[type="tel"]:focus,
				input[type="text"]:focus,
				input[type="time"]:focus,
				input[type="url"]:focus,
				input[type="week"]:focus,
				input[type="checkbox"]:focus,
				input[type="radio"]:focus,
				select:focus,
				textarea:focus {
					border-color: <?php echo esc_attr( $highlight_color ); ?>;
					-webkit-box-shadow: 0 0 2px <?php echo esc_attr( $highlight_color_shadow ); ?>;
					box-shadow: 0 0 2px <?php echo esc_attr( $highlight_color_shadow ); ?>;
				}

				.wp-core-ui .button-primary {
					background: <?php echo esc_attr( $highlight_color ); ?>;
					border-color: <?php echo esc_attr( $highlight_color_hover ); ?>;
					-webkit-box-shadow: 0 1px 0 <?php echo esc_attr( $highlight_color_hover ); ?>;
					box-shadow: 0 1px 0 <?php echo esc_attr( $highlight_color_hover ); ?>;
					color: #fff;
					text-shadow: 0 -1px 1px <?php echo esc_attr( $highlight_color_hover ); ?>,
						1px 0 1px <?php echo esc_attr( $highlight_color_hover ); ?>,
						0 1px 1px <?php echo esc_attr( $highlight_color_hover ); ?>,
						-1px 0 1px <?php echo esc_attr( $highlight_color_hover ); ?>;
				}

				.wp-core-ui .button-primary.hover,
				.wp-core-ui .button-primary:hover,
				.wp-core-ui .button-primary.focus,
				.wp-core-ui .button-primary:focus {
					background: <?php echo esc_attr( $highlight_color_hover ); ?>;
					border-color: <?php echo esc_attr( $highlight_color_hover ); ?>;
					color: #fff;
				}

				.wp-core-ui .button-primary.focus,
				.wp-core-ui .button-primary:focus {
					-webkit-box-shadow: 0 1px 0 <?php echo esc_attr( $highlight_color_hover ); ?>,
						0 0 2px 1px <?php echo esc_attr( $highlight_color_hover ); ?>;
					box-shadow: 0 1px 0 <?php echo esc_attr( $highlight_color_hover ); ?>,
						0 0 2px 1px <?php echo esc_attr( $highlight_color_hover ); ?>;
				}

				.wp-core-ui .button-primary.active,
				.wp-core-ui .button-primary.active:hover,
				.wp-core-ui .button-primary.active:focus,
				.wp-core-ui .button-primary:active {
					background: <?php echo esc_attr( $highlight_color ); ?>;
					border-color: <?php echo esc_attr( $highlight_color_hover ); ?>;
					-webkit-box-shadow: inset 0 2px 0 <?php echo esc_attr( $highlight_color_hover ); ?>;
					box-shadow: inset 0 2px 0 <?php echo esc_attr( $highlight_color_hover ); ?>;
				}

				.wp-core-ui .button-primary[disabled],
				.wp-core-ui .button-primary:disabled,
				.wp-core-ui .button-primary-disabled,
				.wp-core-ui .button-primary.disabled {
					opacity: 0.8;
					color: #ffffff !important;
					background: <?php echo esc_attr( $highlight_color ); ?> !important;
					border-color: <?php echo esc_attr( $highlight_color_hover ); ?> !important;
				}

				.wp-core-ui .button.button-primary.button-hero {
					-webkit-box-shadow: 0 2px 0 <?php echo esc_attr( $highlight_color_hover ); ?>;
					box-shadow: 0 2px 0 <?php echo esc_attr( $highlight_color_hover ); ?>;
				}

				.wp-core-ui .button.button-primary.button-hero.active,
				.wp-core-ui .button.button-primary.button-hero.active:hover,
				.wp-core-ui .button.button-primary.button-hero.active:focus,
				.wp-core-ui .button.button-primary.button-hero:active {
					-webkit-box-shadow: inset 0 3px 0 <?php echo esc_attr( $highlight_color_hover ); ?>;
					box-shadow: inset 0 3px 0 <?php echo esc_attr( $highlight_color_hover ); ?>;
				}
			</style>
			<?php
		}

		// If header image is available, display it instead of the WordPress logo and replace the link accordingly.
		if ( $header_image_url && $header_image_size ) {
			if ( is_string( $header_image_size ) && str_contains( $header_image_size, 'x' ) ) {
				$parts               = explode( 'x', $header_image_size );
				$header_image_width  = (int) $parts[0];
				$header_image_height = (int) $parts[1];
			} else {
				$header_image_width  = (int) $header_image_size;
				$header_image_height = $header_image_width;
			}

			?>
			<style type="text/css">
				.login h1 a {
					background-image: url('<?php echo esc_attr( $header_image_url ); ?>');
					background-size: <?php echo esc_attr( $header_image_width ); ?>px <?php echo esc_attr( $header_image_height ); ?>px;
					width: <?php echo esc_attr( $header_image_width ); ?>px;
					height: <?php echo esc_attr( $header_image_height ); ?>px;
				}
			</style>
			<?php

			add_filter(
				'login_headerurl',
				static function () {
					return home_url( '/' );
				}
			);
			add_filter(
				'login_headertext',
				static function () {
					return get_bloginfo( 'name', 'display' );
				}
			);
		}
	}
);
