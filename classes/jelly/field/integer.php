<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Handles integer data-types
 *
 * @package Jelly
 * @author Jonathan Geiger
 */
abstract class Jelly_Field_Integer extends Jelly_Field
{
	/**
	 * Converts the value to an integer
	 *
	 * @param  mixed $value 
	 * @return int
	 * @author Jonathan Geiger
	 */
	public function set($value)
	{
		return (int)$value;
	}
}
