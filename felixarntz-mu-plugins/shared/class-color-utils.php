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
	 * Converts an RGB array to an HSL array.
	 *
	 * @param array $color Array containing keys 'r', 'g', 'b', with integer values, and optionally 'a' with a float.
	 * @return array Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
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
			's' => (int) round( $saturation ),
			'l' => (int) round( $lightness * 100 ),
		);
		if ( isset( $color['a'] ) ) {
			$result['a'] = $color['a'];
		}
		return $result;
	}

	/**
	 * Converts a HEX value to HSL.
	 *
	 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
	 * @return array Array containing keys 'h', 's', 'l', with integer values.
	 */
	public static function hex_to_hsl( string $color ): array {
		return self::rgb_to_hsl( self::hex_to_rgb( $color ) );
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
	 * @param array $color   Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
	 * @param int   $percent Percentage value between 0 and 100.
	 * @return array Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
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
	 * @param array $color   Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
	 * @param int   $percent Percentage value between 0 and 100.
	 * @return array Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
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
	 * @param array $color   Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
	 * @param int   $percent Percentage value between 0 and 100.
	 * @return array Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
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
	 * @param array $color   Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
	 * @param int   $percent Percentage value between 0 and 100.
	 * @return array Array containing keys 'h', 's', 'l', with integer values, and optionally 'a' with a float.
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
