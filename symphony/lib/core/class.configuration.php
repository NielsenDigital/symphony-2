<?php

	/**
	 * @package core
	 */

	 /**
	  * The Configuration class acts as a property => value store for settings
	  * used throughout Symphony. The result of this class is a string containing
	  * a PHP representation of the properties (and their values) set by the Configuration.
	  * Symphony's configuration file is saved at `CONFIG`. The initial
	  * file is generated by the Symphony installer, and then subsequent use of Symphony
	  * loads in this file for each page view. Like minded properties can be grouped.
	  */
	Class Configuration{

		/**
		 * An associative array of the properties for this Configuration object
		 * @var array
		 */
		private $_properties = array();

		/**
		 * Whether all properties and group keys will be forced to be lowercase.
		 * By default this is false, which makes all properties case sensitive
		 */
		private $_forceLowerCase = false;

		/**
		 * The constructor for the Configuration class takes one parameter,
		 * `$forceLowerCase` which will make all property and
		 * group names lowercase or not. By default they are left to the case
		 * the user provides
		 *
		 * @param boolean $forceLowerCase
		 *  False by default, if true this will make all property and group names
		 *  lowercase
		 */
		public function __construct($forceLowerCase=false){
			$this->_forceLowerCase = $forceLowerCase;
		}

		/**
		 * Setter for the `$this->_properties`. The properties array
		 * can be grouped to be an 'array' of an 'array' of properties. For instance
		 * a 'region' key may be an array of 'properties' (that is name/value), or it
		 * may be a 'value' itself.
		 *
		 * @param string $name
		 *  The name of the property to set, eg 'timezone'
		 * @param string $value
		 *  The value for the property to set, eg. '+10:00'
		 * @param string $group
		 *  The group for this property, eg. 'region'
		 */
		public function set($name, $value, $group = null){
			if($this->_forceLowerCase){
				$name = strtolower($name); $group = strtolower($group);
			}

			$value = stripslashes($value);

			if($group) $this->_properties[$group][$name] = $value;
			else $this->_properties[$name] = $value;
		}

		/**
		 * A quick way to set a large number of properties. Given an array that may
		 * contain 'property' => 'value' or 'group' => array('property' => 'value') or
		 * a combination of both, this will PHP's array_merge with `$this->_properties`
		 *
		 * @param array $array
		 *  An associative array of properties, 'property' => 'value' or 'group' => array(
		 *  'property' => 'value'
		 */
		public function setArray(array $array){
			$array = General::array_map_recursive('stripslashes', $array);
			$this->_properties = array_merge($this->_properties, $array);
		}

		/**
		 * Accessor function for the `$this->_properties`. If the
		 * `$name` is provided, the resulting value will be run through
		 * PHP's stripslashes.
		 *
		 * @param string $name
		 *  The name of the property to retrieve
		 * @param string $group
		 *  The group that this property will be in
		 * @return array|string
		 *  If `$name` or `$group` are not
		 *  provided this function will return the full `$this->_properties`
		 *  array.
		 */
		public function get($name=null, $group=null){

			## Return the whole array if no name or index is requested
			if(!$name && !$group) return $this->_properties;

			if($this->_forceLowerCase){
				$name = strtolower($name); $group = strtolower($group);
			}

			if($group){
				return (isset($this->_properties[$group][$name]) ? $this->_properties[$group][$name] : null);
			}

			return (isset($this->_properties[$name]) ? $this->_properties[$name] : null);
		}

		/**
		 * The remove function will unset a property by `$name`.
		 * It is possible to remove an entire 'group' by passing the group
		 * name as the `$name`
		 *
		 * @param string $name
		 *  The name of the property to unset. This can also be the group name
		 * @param string $group
		 *  The group of the property to unset
		 */
		public function remove($name, $group = null){
			if($this->_forceLowerCase){
				$name = strtolower($name); $group = strtolower($group);
			}

			if($index && isset($this->_properties[$group][$name]))
				unset($this->_properties[$group][$name]);

			elseif($this->_properties[$name])
				unset($this->_properties[$name]);
		}

		/**
		 * Empties all the Configuration values by setting `$this->_properties`
		 * to an empty array
		 */
		public function flush(){
			$this->_properties = array();
		}

		/**
		 * The magic __toString function converts the internal `$this->_properties`
		 * array into a string representation. Symphony generates the `MANIFEST/config.php`
		 * file in this manner. All values are run through PHP's addslashes before saving.
		 *
		 * @return string
		 *  A string that contains a PHP representation of `$this->_properties`.
		 *  This is used by Symphony to write as a file that is then read at a later date.
		 */
		public function __toString(){

			$string = 'array(';
			foreach($this->_properties as $group => $data){
				$string .= "\r\n\r\n\r\n\t\t###### ".strtoupper($group)." ######";
				$string .= "\r\n\t\t'$group' => array(";
				foreach($data as $key => $value){
					$string .= "\r\n\t\t\t'$key' => ".(strlen($value) > 0 ? "'".addslashes($value)."'" : 'null').",";
				}
				$string .= "\r\n\t\t),";
				$string .= "\r\n\t\t########";
			}
			$string .= "\r\n\t)";

			return $string;
		}

	}
