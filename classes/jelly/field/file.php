<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Handles files and file uploads
 *
 * @package Jelly
 * @author Jonathan Geiger
 */
abstract class Jelly_Field_File extends Jelly_Field
{	
	/**
	 * Ensures there is a path for saving set
	 *
	 * @param  array $options 
	 * @author Jonathan Geiger
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);
		
		// Ensure we have path to save to 
		if (empty($this->path) || !is_writable($this->path))
		{
			throw new Kohana_Exception(get_class($this).' must have a `path` property set that points to a writable directory');
		}
		
		// Make sure the path has a trailing slash
		$this->path = rtrim(str_replace('\\', '/', $this->path), '/').'/';
	}
	
	/**
	 * Either uploads a file
	 *
	 * @param  Jelly  $model 
	 * @param  mixed $value 
	 * @return mixed
	 * @author Jonathan Geiger
	 */
	public function save($model, $value)
	{		
		// Upload a file?
		if (upload::valid($value))
		{
			if (FALSE !== ($filename = upload::save($value, NULL, $this->path)))
			{
				$value = $filename;
			}
			else
			{
				$value = $this->default;
			}
		}
		
		return $value;
	}
}
