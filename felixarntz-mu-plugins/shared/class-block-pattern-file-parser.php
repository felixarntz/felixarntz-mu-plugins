<?php
/**
 * Class Felix_Arntz\MU_Plugins\Shared\Block_Pattern_File_Parser
 *
 * @package felixarntz-mu-plugins
 */

namespace Felix_Arntz\MU_Plugins\Shared;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for parsing block patterns from PHP files within a directory.
 */
class Block_Pattern_File_Parser {

	/**
	 * Directory in which the block pattern files reside.
	 *
	 * @var string
	 */
	private $pattern_dir;

	/**
	 * Version identifier for the block pattern directory, used for cache invalidation.
	 *
	 * @var string
	 */
	private $pattern_dir_version;

	/**
	 * Text domain to translate block pattern metadata with.
	 *
	 * @var string
	 */
	private $text_domain;

	/**
	 * Cache hash for the block pattern directory.
	 *
	 * @var string
	 */
	private $cache_hash;

	/**
	 * Constructor.
	 *
	 * @param string $pattern_dir Directory in which the block pattern files reside.
	 * @param string $version     Optional. Version identifier for the block pattern directory, used for cache
	 *                            invalidation. Default is an empty string, which means the version is ignored.
	 * @param string $text_domain Optional. Text domain to translate block pattern metadata with. Default is an empty
	 *                            string, which means the metadata is not translated.
	 */
	public function __construct( string $pattern_dir, string $version = '', string $text_domain = '' ) {
		$this->pattern_dir         = trailingslashit( wp_normalize_path( $pattern_dir ) );
		$this->pattern_dir_version = $version;
		$this->text_domain         = $text_domain;

		$this->cache_hash = md5( str_replace( wp_normalize_path( WP_CONTENT_DIR ), '', $this->pattern_dir ) );
	}

	/**
	 * Registers block patterns from the directory.
	 *
	 * Most of this method is copied from the _register_theme_block_patterns() function.
	 */
	public function register_block_patterns() {
		$registry = \WP_Block_Patterns_Registry::get_instance();
		$patterns = $this->get_block_patterns();

		foreach ( $patterns as $file => $pattern_data ) {
			if ( $registry->is_registered( $pattern_data['slug'] ) ) {
				continue;
			}

			$file_path = $this->pattern_dir . $file;

			if ( ! file_exists( $file_path ) ) {
				_doing_it_wrong(
					__FUNCTION__,
					esc_html(
						sprintf(
							/* translators: %s: file name. */
							__( 'Could not register file "%s" as a block pattern as the file does not exist.', 'default' ),
							$file
						)
					),
					'6.4.0'
				);
				$this->delete_pattern_cache();
				continue;
			}

			$pattern_data['filePath'] = $file_path;

			// Translate the pattern metadata if possible.
			if ( $this->text_domain ) {
				// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain,WordPress.WP.I18n.LowLevelTranslationFunction
				$pattern_data['title'] = translate_with_gettext_context( $pattern_data['title'], 'Pattern title', $this->text_domain );
				if ( ! empty( $pattern_data['description'] ) ) {
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain,WordPress.WP.I18n.LowLevelTranslationFunction
					$pattern_data['description'] = translate_with_gettext_context( $pattern_data['description'], 'Pattern description', $this->text_domain );
				}
			}

			register_block_pattern( $pattern_data['slug'], $pattern_data );
		}
	}

	/**
	 * Gets the block pattern data for the directory.
	 *
	 * This is effectively a copy of the WP_Theme::get_block_patterns() method, but for a custom directory.
	 * The only modifications to the code are:
	 * - The $dirpath is set to the custom directory.
	 * - The development mode is checked for 'plugin' rather than for 'theme'.
	 * - The 'default' text domain is explicitly provided to satisfy WPCS requirements.
	 * - Error escaping has been added to satisfy WPCS requirements.
	 *
	 * @return array Block pattern data.
	 */
	public function get_block_patterns(): array {
		$can_use_cached = ! wp_is_development_mode( 'plugin' );

		$pattern_data = $this->get_pattern_cache();
		if ( is_array( $pattern_data ) ) {
			if ( $can_use_cached ) {
				return $pattern_data;
			}
			// If in development mode, clear pattern cache.
			$this->delete_pattern_cache();
		}

		$dirpath      = $this->pattern_dir;
		$pattern_data = array();

		if ( ! file_exists( $dirpath ) ) {
			if ( $can_use_cached ) {
				$this->set_pattern_cache( $pattern_data );
			}
			return $pattern_data;
		}
		$files = glob( $dirpath . '*.php' );
		if ( ! $files ) {
			if ( $can_use_cached ) {
				$this->set_pattern_cache( $pattern_data );
			}
			return $pattern_data;
		}

		$default_headers = array(
			'title'         => 'Title',
			'slug'          => 'Slug',
			'description'   => 'Description',
			'viewportWidth' => 'Viewport Width',
			'inserter'      => 'Inserter',
			'categories'    => 'Categories',
			'keywords'      => 'Keywords',
			'blockTypes'    => 'Block Types',
			'postTypes'     => 'Post Types',
			'templateTypes' => 'Template Types',
		);

		$properties_to_parse = array(
			'categories',
			'keywords',
			'blockTypes',
			'postTypes',
			'templateTypes',
		);

		foreach ( $files as $file ) {
			$pattern = get_file_data( $file, $default_headers );

			if ( empty( $pattern['slug'] ) ) {
				_doing_it_wrong(
					__FUNCTION__,
					esc_html(
						sprintf(
							/* translators: 1: file name. */
							__( 'Could not register file "%s" as a block pattern ("Slug" field missing)', 'default' ),
							$file
						)
					),
					'6.0.0'
				);
				continue;
			}

			if ( ! preg_match( '/^[A-z0-9\/_-]+$/', $pattern['slug'] ) ) {
				_doing_it_wrong(
					__FUNCTION__,
					esc_html(
						sprintf(
							/* translators: 1: file name; 2: slug value found. */
							__( 'Could not register file "%1$s" as a block pattern (invalid slug "%2$s")', 'default' ),
							$file,
							$pattern['slug']
						)
					),
					'6.0.0'
				);
			}

			// Title is a required property.
			if ( ! $pattern['title'] ) {
				_doing_it_wrong(
					__FUNCTION__,
					esc_html(
						sprintf(
							/* translators: 1: file name. */
							__( 'Could not register file "%s" as a block pattern ("Title" field missing)', 'default' ),
							$file
						)
					),
					'6.0.0'
				);
				continue;
			}

			// For properties of type array, parse data as comma-separated.
			foreach ( $properties_to_parse as $property ) {
				if ( ! empty( $pattern[ $property ] ) ) {
					$pattern[ $property ] = array_filter( wp_parse_list( (string) $pattern[ $property ] ) );
				} else {
					unset( $pattern[ $property ] );
				}
			}

			// Parse properties of type int.
			$property = 'viewportWidth';
			if ( ! empty( $pattern[ $property ] ) ) {
				$pattern[ $property ] = (int) $pattern[ $property ];
			} else {
				unset( $pattern[ $property ] );
			}

			// Parse properties of type bool.
			$property = 'inserter';
			if ( ! empty( $pattern[ $property ] ) ) {
				$pattern[ $property ] = in_array(
					strtolower( $pattern[ $property ] ),
					array( 'yes', 'true' ),
					true
				);
			} else {
				unset( $pattern[ $property ] );
			}

			$key = str_replace( $dirpath, '', $file );

			$pattern_data[ $key ] = $pattern;
		}

		if ( $can_use_cached ) {
			$this->set_pattern_cache( $pattern_data );
		}

		return $pattern_data;
	}

	/**
	 * Gets block pattern cache.
	 *
	 * @return array|false Returns an array of patterns if cache is found, otherwise false.
	 */
	private function get_pattern_cache() {
		$pattern_data = get_site_transient( 'felixarntz_mu_files_patterns-' . $this->cache_hash );

		if ( is_array( $pattern_data ) && $pattern_data['version'] === $this->pattern_dir_version ) {
			return $pattern_data['patterns'];
		}

		return false;
	}

	/**
	 * Sets block pattern cache.
	 *
	 * @param array $patterns Block patterns data to set in cache.
	 */
	private function set_pattern_cache( array $patterns ) {
		$pattern_data = array(
			'version'  => $this->pattern_dir_version,
			'patterns' => $patterns,
		);

		set_site_transient( 'felixarntz_mu_files_patterns-' . $this->cache_hash, $pattern_data, 1800 );
	}

	/**
	 * Clears block pattern cache.
	 */
	public function delete_pattern_cache() {
		delete_site_transient( 'felixarntz_mu_files_patterns-' . $this->cache_hash );
	}
}
