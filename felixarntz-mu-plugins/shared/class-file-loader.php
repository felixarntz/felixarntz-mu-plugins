<?php
/**
 * Class Felix_Arntz\MU_Plugins\Shared\File_Loader
 *
 * @package felixarntz-mu-plugins
 */

namespace Felix_Arntz\MU_Plugins\Shared;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for loading feature files.
 */
class File_Loader {

	/**
	 * Files directory.
	 *
	 * @var string
	 */
	protected $files_dir;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->files_dir = dirname( __DIR__ ) . '/';
	}

	/**
	 * Loads the given feature file.
	 *
	 * @param string $file PHP file name, relative to the files subdirectory.
	 */
	public function load_file( string $file ) {
		$file = $this->files_dir . $file;

		if ( ! file_exists( $file ) ) {
			return;
		}

		require_once $file;
	}

	/**
	 * Loads all available feature files.
	 */
	public function load_all_files() {
		$files = glob( $this->files_dir . '*.php' );

		foreach ( $files as $file ) {
			require_once $file;
		}
	}
}
