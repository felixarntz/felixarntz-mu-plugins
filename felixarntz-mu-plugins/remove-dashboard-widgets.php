<?php
/**
 * Plugin Name: Remove Dashboard Widgets
 * Plugin URI: https://github.com/felixarntz/felixarntz-mu-plugins
 * Description: Removes all default widgets from the WordPress dashboard.
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
	'do_meta_boxes',
	static function ( $screen_id ) {
		global $wp_meta_boxes;

		if ( 'dashboard' !== $screen_id ) {
			return;
		}

		$config                    = Shared\Config::instance();
		$default_widgets_to_remove = $config->get( 'remove_default_dashboard_widgets', true );
		if ( $default_widgets_to_remove ) {
			$default_widgets = array(
				'dashboard_right_now'         => 'normal',
				'network_dashboard_right_now' => 'normal',
				'dashboard_activity'          => 'normal',
				'dashboard_quick_press'       => 'side',
				'dashboard_primary'           => 'side',
			);

			// If the value is not a selection of default widgets, wipe all of them.
			if ( ! is_array( $default_widgets_to_remove ) ) {
				$default_widgets_to_remove = array_merge(
					array_keys( $default_widgets ),
					array( 'dashboard_site_health', 'welcome_panel' )
				);
			}

			foreach ( $default_widgets_to_remove as $widget_id ) {
				if ( isset( $default_widgets[ $widget_id ] ) ) {
					remove_meta_box( $widget_id, $screen_id, $default_widgets[ $widget_id ] );
				} elseif ( 'dashboard_site_health' === $widget_id ) {
					// Remove Site Health unless there are critical issues or recommendations.
					if ( isset( $wp_meta_boxes[ $screen_id ]['normal']['core']['dashboard_site_health'] ) ) {
						$get_issues = get_transient( 'health-check-site-status-result' );
						if ( false === $get_issues ) {
							remove_meta_box( 'dashboard_site_health', $screen_id, 'normal' );
						} else {
							$issue_counts = json_decode( $get_issues, true );
							if ( empty( $issue_counts['critical'] ) && empty( $issue_counts['recommended'] ) ) {
								remove_meta_box( 'dashboard_site_health', $screen_id, 'normal' );
							}
						}
					}
				} elseif ( 'welcome_panel' === $widget_id ) {
					remove_action( 'welcome_panel', 'wp_welcome_panel' );
				}
			}
		}

		$remove_additional_widgets = $config->get( 'remove_dashboard_widgets', array() );
		if ( ! $remove_additional_widgets ) {
			return;
		}
		foreach ( $remove_additional_widgets as $widget_id => $context ) {
			remove_meta_box( $widget_id, $screen_id, $context );
		}
	}
);
