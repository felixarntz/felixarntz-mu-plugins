<?php
/**
 * Class Felix_Arntz\MU_Plugins\Shared\Color_Utils
 *
 * @package felixarntz-mu-plugins
 */

namespace Felix_Arntz\MU_Plugins\Shared;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class with utility methods for managing colors.
 */
class Color_Utils {

	/**
	 * Converts a HEX value to RGB.
	 *
	 * Mostly copied from Twenty Sixteen theme.
	 *
	 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
	 * @return array Array containing keys 'r', 'g', 'b', with integer values.
	 */
	public static function hex_to_rgb( string $color ): array {
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

	/**
	 * Converts an RGB array to a HEX value.
	 *
	 * @param array $color Array containing keys 'r', 'g', 'b', with integer value. If an 'a' key is present, it will be stripped.
	 * @return string Hex color, in 3- or 6-digit hexadecimal form.
	 */
	public static function rgb_to_hex( array $color ): string {
		$result  = str_pad( dechex( $color['r'] ), 2, '0', STR_PAD_LEFT );
		$result .= str_pad( dechex( $color['g'] ), 2, '0', STR_PAD_LEFT );
		$result .= str_pad( dechex( $color['b'] ), 2, '0', STR_PAD_LEFT );

		return $result;
	}

	/**
	 * Converts an RGB array to an HSL array.
	 *
	 * @param array $color Array containing keys 'r', 'g', 'b', with integer values, and optionally 'a' with a float.
	 * @return array Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 */
	public static function rgb_to_hsl( array $color ): array {
		$r = $color['r'] / 255;
		$g = $color['g'] / 255;
		$b = $color['b'] / 255;

		// Find min and max RGB values.
		$cmin = min( $r, $g, $b );
		$cmax = max( $r, $g, $b );

		// Calculate delta.
		$delta = $cmax - $cmin;

		// Calculate hue.
		$hue = 0;
		if ( $delta > 0 ) {
			if ( $cmax === $r ) {
				$hue = fmod( ( $g - $b ) / $delta, 6 );
			} elseif ( $cmax === $g ) {
				$hue = ( $b - $r ) / $delta + 2;
			} else {
				$hue = ( $r - $g ) / $delta + 4;
			}
			$hue *= 60;
			$hue  = fmod( $hue, 360 );
		}
		if ( $hue < 0 ) {
			$hue += 360;
		}

		// Calculate lightness.
		$lightness = ( $cmax + $cmin ) / 2;

		// Calculate saturation.
		$saturation = 0;
		if ( 0 !== $delta ) {
			$saturation  = $lightness <= 0.5 ? $delta / ( $cmax + $cmin ) : $delta / ( 2 - $cmax - $cmin );
			$saturation *= 100;
		}

		$result = array(
			'h' => (int) round( $hue ),
			's' => round( $saturation, 1 ),
			'l' => round( $lightness * 100, 1 ),
		);

		if ( isset( $color['a'] ) ) {
			$result['a'] = $color['a'];
		}

		return $result;
	}

	/**
	 * Converts an HSL array to an RGB array.
	 *
	 * @param array $color Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 * @return array Array containing keys 'r', 'g', 'b', with integer values, and optionally 'a' with a float.
	 */
	public static function hsl_to_rgb( array $color ): array {
		$c = ( 1.0 - abs( $color['l'] / 100 * 2 - 1.0 ) ) * $color['s'] / 100;
		$x = $c * ( 1 - abs( fmod( $color['h'] / 60, 2 ) - 1 ) );
		$m = $color['l'] / 100 - $c / 2;

		if ( $color['h'] < 60 ) {
			$result = array(
				'r' => $c + $m,
				'g' => $x + $m,
				'b' => $m,
			);
		} elseif ( $color['h'] < 120 ) {
			$result = array(
				'r' => $x + $m,
				'g' => $c + $m,
				'b' => $m,
			);
		} elseif ( $color['h'] < 180 ) {
			$result = array(
				'r' => $m,
				'g' => $c + $m,
				'b' => $x + $m,
			);
		} elseif ( $color['h'] < 240 ) {
			$result = array(
				'r' => $m,
				'g' => $x + $m,
				'b' => $c + $m,
			);
		} elseif ( $color['h'] < 300 ) {
			$result = array(
				'r' => $x + $m,
				'g' => $m,
				'b' => $c + $m,
			);
		} else {
			$result = array(
				'r' => $c + $m,
				'g' => $m,
				'b' => $x + $m,
			);
		}

		foreach ( $result as $key => $value ) {
			$result[ $key ] = (int) round( $value * 255 );
		}
		if ( isset( $color['a'] ) ) {
			$result['a'] = $color['a'];
		}

		return $result;
	}

	/**
	 * Converts a HEX value to HSL.
	 *
	 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
	 * @return array Array containing keys 'h' (integer), 's', 'l' (float).
	 */
	public static function hex_to_hsl( string $color ): array {
		return self::rgb_to_hsl( self::hex_to_rgb( $color ) );
	}

	/**
	 * Converts an HSL array to a HEX value.
	 *
	 * @param array $color Array containing keys 'h' (integer), 's', 'l' (float). If an 'a' key is present, it will be stripped.
	 * @return string Hex color, in 3- or 6-digit hexadecimal form.
	 */
	public static function hsl_to_hex( array $color ): string {
		return self::rgb_to_hex( self::hsl_to_rgb( $color ) );
	}

	/**
	 * Sets the opacity on an RGB color, as such forcing it to RGBA.
	 *
	 * @param array $color   Array containing keys 'r', 'g', 'b', with integer values, and optionally 'a' with a float.
	 * @param int   $opacity Opacity as a percentage value between 0 and 100.
	 * @return array Array containing keys 'r', 'g', 'b', with integer values, and 'a' with a float.
	 */
	public static function set_rgb_opacity( array $color, int $opacity ): array {
		$color['a'] = $opacity / 100.0;
		return $color;
	}

	/**
	 * Lightens an HSL color.
	 *
	 * @param array $color   Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 * @param int   $percent Percentage value between 0 and 100.
	 * @return array Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 */
	public static function lighten_hsl( array $color, int $percent ): array {
		if ( $color['l'] + $percent > 100 ) {
			$color['l'] = 100;
		} else {
			$color['l'] += $percent;
		}
		return $color;
	}

	/**
	 * Darkens an HSL color.
	 *
	 * @param array $color   Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 * @param int   $percent Percentage value between 0 and 100.
	 * @return array Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 */
	public static function darken_hsl( array $color, int $percent ): array {
		if ( $color['l'] - $percent < 0 ) {
			$color['l'] = 0;
		} else {
			$color['l'] -= $percent;
		}
		return $color;
	}

	/**
	 * Saturates an HSL color.
	 *
	 * @param array $color   Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 * @param int   $percent Percentage value between 0 and 100.
	 * @return array Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 */
	public static function saturate_hsl( array $color, int $percent ): array {
		if ( $color['s'] + $percent > 100 ) {
			$color['s'] = 100;
		} else {
			$color['s'] += $percent;
		}
		return $color;
	}

	/**
	 * Desaturates an HSL color.
	 *
	 * @param array $color   Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 * @param int   $percent Percentage value between 0 and 100.
	 * @return array Array containing keys 'h' (integer), 's', 'l' (float), and optionally 'a' (float).
	 */
	public static function desaturate_hsl( array $color, int $percent ): array {
		if ( $color['s'] - $percent < 0 ) {
			$color['s'] = 0;
		} else {
			$color['s'] -= $percent;
		}
		return $color;
	}

	/**
	 * Returns the CSS string representation of a color.
	 *
	 * @param string|array $color Either a hex color string, or an RGB or HSL array.
	 * @return string CSS color string representation.
	 */
	public static function to_css_string( $color ): string {
		if ( is_array( $color ) ) {
			// It is an HSL array.
			if ( isset( $color['h'] ) ) {
				if ( isset( $color['a'] ) ) {
					return 'hsla(' . $color['h'] . ', ' . $color['s'] . '%, ' . $color['l'] . '%, ' . $color['a'] . ')';
				}
				return 'hsl(' . $color['h'] . ', ' . $color['s'] . '%, ' . $color['l'] . '%)';
			}

			// It is an RGB array.
			if ( isset( $color['a'] ) ) {
				return 'rgba(' . $color['r'] . ', ' . $color['g'] . ', ' . $color['b'] . ', ' . $color['a'] . ')';
			}
			return 'rgb(' . $color['r'] . ', ' . $color['g'] . ', ' . $color['b'] . ')';
		}

		// It is a hex string.
		return '#' . ltrim( $color, '#' );
	}
}
