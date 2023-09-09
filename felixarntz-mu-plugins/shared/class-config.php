<?php
/**
 * Class Felix_Arntz\MU_Plugins\Shared\Config
 *
 * @package felixarntz-mu-plugins
 */

namespace Felix_Arntz\MU_Plugins\Shared;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for storing configuration as key value pairs.
 */
class Config implements ArrayAccess, Countable, IteratorAggregate {

	/**
	 * Configuration options.
	 *
	 * @var array
	 */
	protected $opt = array();

	/**
	 * Main instance.
	 *
	 * @var Config
	 */
	protected static $instance;

	/**
	 * Gets the main instance of the config.
	 *
	 * @return Config Main instance.
	 */
	public static function instance(): Config {
		if ( null === static::$instance ) {
			static::$instance = new static( array() );
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @param array $options Initial options to set.
	 */
	public function __construct( array $options ) {
		$this->merge( $options );
	}

	/**
	 * Gets a config value.
	 *
	 * @param string $key           Config key.
	 * @param mixed  $default_value Optional. Default to return if no value set for the key.
	 * @return mixed Value for the key, or $default_value.
	 */
	public function get( $key, $default_value = null ) {
		if ( $this->offsetExists( $key ) ) {
			return $this->opt[ $key ];
		}

		return $default_value;
	}

	/**
	 * Merges the given config options.
	 *
	 * @param array $options Options to merge.
	 */
	public function merge( array $options ) {
		foreach ( $options as $key => $value ) {
			if ( $this->offsetExists( $key ) ) {
				continue;
			}

			$this->offsetSet( $key, $value );
		}
	}

	/**
	 * Forcefully merges the given config options.
	 *
	 * This will overwrite options that already exist.
	 *
	 * @param array $options Options to merge.
	 */
	public function forceMerge( array $options ) {
		foreach ( $options as $key => $value ) {
			$this->offsetSet( $key, $value );
		}
	}

	/**
	 * Gets the internal array of config options.
	 *
	 * @return array Associative array of key value pairs.
	 */
	public function all() {
		return $this->opt;
	}

	/**
	 * Gets all config options.
	 *
	 * @return Traversable Associative array of key value pairs.
	 */
	#[\ReturnTypeWillChange]
	public function getIterator() {
		return $this->opt;
	}

	/**
	 * Gets number of options in the config.
	 *
	 * @return int
	 */
	#[\ReturnTypeWillChange]
	public function count() {
		return count( $this->opt );
	}

	/**
	 * Gets a config value.
	 *
	 * @param string $key Config key.
	 * @return mixed Value for the key, or null if not set.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Sets a config value.
	 *
	 * @param string $key   Config key.
	 * @param mixed  $value Value to set.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		$this->opt[ $key ] = $value;
	}

	/**
	 * Checks whether a config value exists.
	 *
	 * @param string $key Config key.
	 * @return bool True if value is set for the key, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return array_key_exists( $key, $this->opt );
	}

	/**
	 * Deletes a config value.
	 *
	 * @param string $key Config key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		unset( $this->opt[ $key ] );
	}

	/**
	 * Gets a config value.
	 *
	 * @param string $key Config key.
	 * @return mixed Value for the key, or null if not set.
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Sets a config value.
	 *
	 * @param string $key   Config key.
	 * @param mixed  $value Value to set.
	 */
	public function __set( $key, $value ) {
		$this->offsetSet( $key, $value );
	}

	/**
	 * Checks whether a config value exists.
	 *
	 * @param string $key Config key.
	 * @return bool True if value is set for the key, false otherwise.
	 */
	public function __isset( $key ) {
		return $this->offsetExists( $key );
	}

	/**
	 * Deletes a config value.
	 *
	 * @param string $key Config key.
	 */
	public function __unset( $key ) {
		$this->offsetUnset( $key );
	}
}
