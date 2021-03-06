<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Handles belongs to relationships
 *
 * @package Jelly
 * @author Jonathan Geiger
 */
abstract class Jelly_Field_BelongsTo extends Jelly_Field_Relationship implements Jelly_Behavior_Field_Joinable
{	
	/**
	 * @var boolean Defaults belongs_to's to in the database
	 */
	public $in_db = TRUE;
	
	/**
	 * This is expected to contain an assoc. array containing the key 
	 * 'model', and the key 'column'
	 * 
	 * If they do not exist, they will be filled in with sensible defaults 
	 * derived from the field's name. If 'model' is empty it is set to the 
	 * singularized name of the field. If 'column' is empty, it is set to 'id'.
	 * 
	 * `'model' => 'a model to use as the foreign association'`
	 * 
	 * `'column' => 'the column (or alias) that is the foreign model's primary key'`
	 *
	 * @var array
	 */
	public $foreign = array();
	
	/**
	 * Automatically sets foreign to sensible defaults
	 *
	 * @param  string $model 
	 * @param  string $column 
	 * @return void
	 * @author Jonathan Geiger
	 */
	public function initialize($model, $column)
	{
		// Default to the name of the column
		if (empty($this->foreign['model']))
		{
			$this->foreign['model'] = $column;
		}
		
		// Default to foreign['model'] plus _id
		if (empty($this->column))
		{
			$this->column = $this->foreign['model'].'_id';
		}
		
		// Default to 'id'
		if (empty($this->foreign['column']))
		{
			$this->foreign['column'] = 'id';
		}
		
		// Column is set and won't be overridden
		parent::initialize($model, $column);
	}
	
	/**
	 * Returns the primary key of the model passed. 
	 * 
	 * Straight primary keys are also accepted.
	 * 
	 * @param  mixed $value
	 * @return int|string
	 * @author Jonathan Geiger
	 */
	public function set($value)
	{
		if (is_object($value))
		{
			$value = $value->id();
		}
		
		return (is_numeric($value)) ? (int) $value : (string) $value;
	}
	
	/**
	 * Returns the jelly model that this model belongs to
	 *
	 * @param  string $model 
	 * @param  string $value 
	 * @return Jelly
	 * @author Jonathan Geiger
	 */
	public function get($model, $value)
	{
		// Return a real category object
		return Jelly::factory($this->foreign['model'])
				->limit(1, TRUE)
				->where($this->foreign['column'], '=', $value);
	}
	
	/**
	 * Implementation of Jelly_Behavior_Field_Joinable
	 *
	 * @param  Jelly  $model 
	 * @param  string $relation 
	 * @param  string $target_path 
	 * @param  string $parent_path 
	 * @return void
	 * @author Jonathan Geiger
	 */
	public function with($model, $relation, $target_path, $parent_path)
	{
		$meta = Jelly_meta::get($this->foreign['model']);

		// Fields have to be aliased since we don't necessarily know the model from the path
		$target_column = Jelly_Meta::column($this->foreign['model'], $meta->primary_key, FALSE);
		$parent_column = Jelly_Meta::column($this->model, $this->foreign['column'], FALSE);
		
		$join_col1 = $target_path.'.'.$target_column;
		$join_col2 = $parent_path.'.'.$parent_column;
				
		$model
			->join(array($meta->table, $target_path), 'LEFT')
			->on($join_col1, '=', $join_col2);
	}
}
