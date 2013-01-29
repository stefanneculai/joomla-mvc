<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */

defined('_JEXEC') or die;

/**
 * The Document class.
 *
 * @since  1.0
 */
class TadaModel extends JModelBase
{
	protected $data;

	protected $object_name;

	protected $db;

	/**
	 * The constructor class
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		parent::__construct(null);

		$this->object_name = get_class($this);

		$this->db = JFactory::getDbo();

		$this->data = new stdClass;
	}

	/**
	 * Method to save object.
	 *
	 * @since   1.0
	 *
	 * @return  mixed
	 */
	public function save()
	{
		if ($this->id != null)
		{
			return $this->update();
		}

		// Get a string inflector.
		$stringInflector = JStringInflector::getInstance();

		$query = $this->db->getQuery(true);
		$query->insert($this->db->quoteName('#__' . $stringInflector->toPlural(strtolower($this->object_name))));

		$columns = array();
		$values = '';
		$i = 0;
		foreach ($this->data as $column => $value)
		{
			array_push($columns, $this->db->quoteName($column));
			$values .= $this->db->quote($value, true);
			if (++$i != count(get_object_vars($this->data)))
			{
				$values .= ',';
			}
		}

		$query->columns($columns);
		$query->values($values);
		$this->db->setQuery($query);
		$this->db->execute();

		return $this->load($this->db->insertid());
	}

	/**
	 * Method to update object.
	 *
	 * @since   1.0
	 *
	 * @return  TadaModel
	 */
	public function update()
	{
		// Get a string inflector.
		$stringInflector = JStringInflector::getInstance();
		$this->db->updateObject('#__' . $stringInflector->toPlural(strtolower($this->object_name)), $this->data, 'id', true);

		return $this;
	}

	/**
	 * Method to delete object.
	 *
	 * @since   1.0
	 *
	 * @return  boolean  The success status.
	 */
	public function delete()
	{
		$stringInflector = JStringInflector::getInstance();

		$query = $this->db->getQuery(true);

		$query->delete();
		$query->from($this->db->quoteName('#__' . $stringInflector->toPlural(strtolower($this->object_name))));
		$query->where($this->db->quoteName('id') . ' = ' . (int) $this->id);

		$this->db->setQuery($query);

		if ($this->db->execute())
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to load object.
	 *
	 * @param   integer  $id      The id of the object in database.
	 * @param   boolean  $update  Update or not the current object.
	 *
	 * @since   1.0
	 *
	 * @return  mixed
	 */
	public function load($id, $update = true)
	{
		// Get a string inflector.
		$stringInflector = JStringInflector::getInstance();

		$query = $this->db->getQuery(true);
		$query->select('*');
		$query->from($this->db->quoteName('#__' . $stringInflector->toPlural(strtolower($this->object_name))));
		$query->where($this->db->quoteName('id') . ' = ' . (int) $id);

		$this->db->setQuery($query);

		$object = $this->db->loadObject();

		if ($object != null)
		{
			if ($update == true)
			{
				foreach ($object as $key => $value)
				{
					$this->{$key} = $value;
				}

				return $this;
			}
			else
			{
				$className = get_class($this);
				$new_object = new $className;

				foreach ($object as $key => $value)
				{
					$new_object->{$key} = $value;
				}

				return $new_object;
			}
		}
		else
		{
			return null;
		}
	}

	/**
	 * Method to find objects.
	 *
	 * @param   array  $conditions  The conditions to find objects by.
	 *
	 * @since   1.0
	 *
	 * @return  mixed
	 */
	public function find($conditions = array())
	{
		if (empty($conditions))
		{
			return null;
		}
		else
		{
			// Get a string inflector.
			$stringInflector = JStringInflector::getInstance();

			$query = $this->db->getQuery(true);
			$query->select('*');
			$query->from($this->db->quoteName('#__' . $stringInflector->toPlural(strtolower($this->object_name))));

			foreach ($conditions as $condition => $value)
			{
				$query->where($this->db->quoteName($condition) . ' = ' . $this->db->quote($value));
			}

			$this->db->setQuery($query);
			$this->db->execte();

			$found_objects = array();
			$objects = $this->db->loadObjectList();

			foreach ($objects as $object)
			{
				array_push($found_objects, $this->load($object->id, false));
			}

			return $found_objects;
		}
	}

	/**
	 * Setter.
	 *
	 * @param   unknown  $name   The name of the var.
	 * @param   unknown  $value  The value of the var.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	public function __set($name, $value)
	{
		$this->data->{$name} = $value;
	}

	/**
	 * Getter.
	 *
	 * @param   unknown  $name  The name of the var.
	 *
	 * @since   1.0
	 *
	 * @return  mixed
	 */
	public function __get($name)
	{
		if (property_exists($this->data, $name))
		{
			return $this->data->{$name};
		}
		return null;
	}

	/**
	 * Method to execute a multi query.
	 *
	 * @param   string  $query  The query.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	protected function multiQuery($query)
	{
		$this->db = JFactory::getDbo();
		$connection = $this->db->getConnection();
		$connection->multi_query($query);
		while ($connection->more_results())
		{
			$connection->next_result();
		}
	}
}
