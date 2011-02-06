<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Hive is an intelligent modeling system for Kohana.
 *
 * @package    Hive
 * @category   Base
 * @author     Woody Gilk <woody@wingsc.com>
 * @copyright  (c) 2011 Woody Gilk
 * @license    MIT
 */
class Hive_Storage extends ArrayObject {

	/**
	 * @var  array  original values
	 */
	protected $_original = array();

	public function __construct(array $data)
	{
		return parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * Get the current data without the object wrapper.
	 *
	 *     $data = $storage->as_array();
	 *
	 * @return  array
	 */
	public function as_array()
	{
		return $this->getArrayCopy();
	}

	/**
	 * Get the current changes between the original and current data sets.
	 *
	 * Each row in the array will be:
	 *
	 *     string $name => array(mixed $original, mixed $current)
	 *
	 * To handle this in a foreach:
	 *
	 *     foreach ($changed as $name => $data)
	 *     {
	 *         list($original, $current) = $data;
	 *
	 *         // ...
	 *
	 *     }
	 *
	 * @return arrray
	 */
	public function changed()
	{
		$changed = array();

		foreach ($this->_original as $name => $original)
		{
			$changed[$name] = array($original, $this[$name]);
		}

		return $changed;
	}

	/**
	 * Check if a field has been changed.
	 *
	 * @return  boolean
	 */
	public function is_changed($name)
	{
		return array_key_exists($name, $this->_original);
	}

	/**
	 * Clear original values. Do this after the stored changes have been saved.
	 *
	 *     $storage->compact();
	 *
	 * @return   void
	 */
	public function compact()
	{
		// Clear out original values
		$this->_original = array();
	}

	public function offsetSet($name, $value)
	{
		if ($this[$name] !== $value)
		{
			if (array_key_exists($name, $this->_original))
			{
				if ($this->_original[$name] === $value)
				{
					// Value is changing back to the original value
					unset($this->_original[$name]);
				}
			}
			else
			{
				// The original value is being changed
				$this->_original[$name] = $this[$name];
			}
		}

		return parent::offsetSet($name, $value);
	}

} // End Hive_Storage

