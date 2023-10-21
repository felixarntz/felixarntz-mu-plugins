<?php
/**
 * Class Felix_Arntz\MU_Plugins\Shared\Admin_Menu
 *
 * @package felixarntz-mu-plugins
 */

namespace Felix_Arntz\MU_Plugins\Shared;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for modifying items in the WordPress admin menu.
 */
class Admin_Menu {

	/**
	 * Map of menu item slugs and their indexes.
	 *
	 * @var array
	 */
	protected $menu_map;

	/**
	 * Main instance.
	 *
	 * @var Admin_Menu
	 */
	protected static $instance;

	/**
	 * Gets the main instance of the config.
	 *
	 * @return Admin_Menu Main instance.
	 */
	public static function instance(): Admin_Menu {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->refresh_menu_map();
	}

	/**
	 * Gets a menu page's data.
	 *
	 * @param string $menu_slug The menu slug or file.
	 * @return array Menu item data, or empty array if the menu page does not exist.
	 */
	public function get_menu_page( string $menu_slug ): array {
		global $menu;

		if ( ! isset( $this->menu_map[ $menu_slug ] ) ) {
			return array();
		}

		return $menu[ $this->menu_map[ $menu_slug ] ];
	}

	/**
	 * Gets a menu page's submenu items.
	 *
	 * @param string $menu_slug The menu slug or file.
	 * @return array List of submenu items data, or empty array if the menu doesn't exist.
	 */
	public function get_submenu_pages( string $menu_slug ): array {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return array();
		}

		return $submenu[ $menu_slug ];
	}

	/**
	 * Gets a submenu page's data.
	 *
	 * @param string $menu_slug    The parent menu slug or file.
	 * @param string $submenu_slug The submenu slug or file.
	 * @return array Submenu item data, or empty array if the submenu page does not exist.
	 */
	public function get_submenu_page( string $menu_slug, string $submenu_slug ): array {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return array();
		}

		foreach ( $submenu[ $menu_slug ] as $index => $item ) {
			if ( $submenu_slug === $item[2] ) {
				return $item;
			}
		}

		return array();
	}

	/**
	 * Gets the first submenu page's data in the given submenu.
	 *
	 * @param string $menu_slug The parent menu slug or file.
	 * @return array Submenu item data, or empty array if the submenu does not exist.
	 */
	public function get_first_submenu_page( string $menu_slug ): array {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return array();
		}

		if ( ! $submenu[ $menu_slug ] ) {
			return array();
		}

		return reset( $submenu[ $menu_slug ] );
	}

	/**
	 * Gets the number of submenu pages in the given submenu.
	 *
	 * @param string $menu_slug The parent menu slug or file.
	 * @return int Number of submenu pages, or 0 if the submenu does not exist.
	 */
	public function get_submenu_page_count( string $menu_slug ): int {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return 0;
		}

		return count( $submenu[ $menu_slug ] );
	}

	/**
	 * Removes a menu page and its submenu.
	 *
	 * @param string $menu_slug The menu slug or file.
	 * @return bool True on success, false on failure.
	 */
	public function remove_menu_page( string $menu_slug ): bool {
		global $menu, $submenu;

		if ( ! isset( $this->menu_map[ $menu_slug ] ) ) {
			return false;
		}

		unset( $menu[ $this->menu_map[ $menu_slug ] ] );
		unset( $this->menu_map[ $menu_slug ] );
		if ( isset( $submenu[ $menu_slug ] ) ) {
			unset( $submenu[ $menu_slug ] );
		}
		return true;
	}

	/**
	 * Removes a submenu page.
	 *
	 * If this is the only submenu page in the menu, the menu will also be removed.
	 *
	 * @param string $menu_slug    The parent menu slug or file.
	 * @param string $submenu_slug The submenu slug or file.
	 * @return bool True on success, false on failure.
	 */
	public function remove_submenu_page( string $menu_slug, string $submenu_slug ): bool {
		global $submenu;

		if ( remove_submenu_page( $menu_slug, $submenu_slug ) ) {
			if ( count( $submenu[ $menu_slug ] ) === 0 ) {
				$this->remove_menu_page( $menu_slug );
			}
			return true;
		}

		return false;
	}

	/**
	 * Updates a menu page's title.
	 *
	 * @param string $menu_slug The menu slug or file.
	 * @param string $new_title New title to assign.
	 * @return bool True on success, false on failure.
	 */
	public function update_menu_page_title( string $menu_slug, string $new_title ): bool {
		global $menu;

		if ( ! isset( $this->menu_map[ $menu_slug ] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$menu[ $this->menu_map[ $menu_slug ] ][0] = $new_title;
		return true;
	}

	/**
	 * Updates a menu page's required capability.
	 *
	 * @param string $menu_slug The menu slug or file.
	 * @param string $new_cap   New capability to assign.
	 * @return bool True on success, false on failure.
	 */
	public function update_menu_page_cap( string $menu_slug, string $new_cap ): bool {
		global $menu;

		if ( ! isset( $this->menu_map[ $menu_slug ] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$menu[ $this->menu_map[ $menu_slug ] ][1] = $new_cap;
		return true;
	}

	/**
	 * Updates a submenu page's title.
	 *
	 * @param string $menu_slug    The parent menu slug or file.
	 * @param string $submenu_slug The submenu slug or file.
	 * @param string $new_title    New title to assign.
	 * @return bool True on success, false on failure.
	 */
	public function update_submenu_page_title( string $menu_slug, string $submenu_slug, string $new_title ): bool {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return false;
		}

		foreach ( $submenu[ $menu_slug ] as $index => $item ) {
			if ( $submenu_slug === $item[2] ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$submenu[ $menu_slug ][ $index ][0] = $new_title;
				return true;
			}
		}

		return false;
	}

	/**
	 * Updates a submenu page's required capability.
	 *
	 * @param string $menu_slug    The parent menu slug or file.
	 * @param string $submenu_slug The submenu slug or file.
	 * @param string $new_cap      New capability to assign.
	 * @return bool True on success, false on failure.
	 */
	public function update_submenu_page_cap( string $menu_slug, string $submenu_slug, string $new_cap ): bool {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return false;
		}

		foreach ( $submenu[ $menu_slug ] as $index => $item ) {
			if ( $submenu_slug === $item[2] ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$submenu[ $menu_slug ][ $index ][1] = $new_cap;
				return true;
			}
		}

		return false;
	}

	/**
	 * Modifies a submenu page to another parent menu.
	 *
	 * @param string   $menu_slug     The parent menu slug or file.
	 * @param string   $submenu_slug  The submenu slug or file.
	 * @param string   $new_menu_slug The new parent menu slug or file.
	 * @param int|null $target_index  Optional. Target index where in the new submenu to add the item. By default, it
	 *                                will be appended. Default null.
	 * @return bool True on success, false on failure.
	 */
	public function move_submenu_page( string $menu_slug, string $submenu_slug, string $new_menu_slug, $target_index = null ): bool {
		global $submenu, $_wp_submenu_nopriv, $_registered_pages, $_parent_pages;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return false;
		}

		// Bail if the new menu does not exist.
		if ( ! isset( $_parent_pages[ $new_menu_slug ] ) && ! isset( $submenu[ $new_menu_slug ] ) && ! isset( $_wp_submenu_nopriv[ $new_menu_slug ] ) ) {
			return false;
		}

		foreach ( $submenu[ $menu_slug ] as $index => $item ) {
			if ( $submenu_slug === $item[2] ) {
				unset( $submenu[ $menu_slug ][ $index ] );

				if ( ! isset( $submenu[ $new_menu_slug ] ) ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$submenu[ $new_menu_slug ] = array();
				}

				if ( $target_index ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$submenu[ $new_menu_slug ][ $target_index ] = $item;
				} else {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$submenu[ $new_menu_slug ][] = $item;
				}

				if ( count( $submenu[ $menu_slug ] ) === 0 ) {
					$this->remove_menu_page( $menu_slug );
				}

				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$_registered_pages[ get_plugin_page_hookname( $submenu_slug, $new_menu_slug ) ] = true;

				return true;
			}
		}

		return false;
	}

	/**
	 * Refreshes a menu page's data based on its first submenu item.
	 *
	 * @param string $menu_slug The menu slug or title.
	 * @return bool True on success, false on failure.
	 */
	public function refresh_menu_page_data( string $menu_slug ): bool {
		global $menu;

		if ( ! isset( $this->menu_map[ $menu_slug ] ) ) {
			return false;
		}

		$submenu_page = $this->get_first_submenu_page( $menu_slug );
		if ( ! $submenu_page ) {
			return false;
		}

		$menu_index = $this->menu_map[ $menu_slug ];
		foreach ( $submenu_page as $field_index => $field_value ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$menu[ $menu_index ][ $field_index ] = $field_value;
		}

		// If the menu slug changed, refresh the internal menu index map accordingly.
		if ( $menu[ $menu_index ][2] !== $menu_slug ) {
			$this->menu_map[ $menu[ $menu_index ][2] ] = $menu_index;
			unset( $this->menu_map[ $menu_slug ] );
		}

		return true;
	}

	/**
	 * Refreshes a menu page's data based on its first submenu item.
	 *
	 * @param string $menu_slug The menu slug or title.
	 * @return bool True on success, false on failure.
	 */
	public function sort_submenu( string $menu_slug ): bool {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return false;
		}

		ksort( $submenu[ $menu_slug ] );
		return true;
	}

	/**
	 * Refreshes the internal map of menu items and their indexes.
	 */
	private function refresh_menu_map() {
		global $menu;

		$this->menu_map = array();
		foreach ( $menu as $index => $item ) {
			$this->menu_map[ $item[2] ] = $index;
		}
	}
}
