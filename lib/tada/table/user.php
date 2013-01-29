<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */

defined('JPATH_PLATFORM') or die;

/**
 * The UserTable class.
 *
 * @since  1.0
 */
class TadaTableUser extends JTableUser
{
	/**
	 * Method to load a user, user groups, and any other necessary data
	 * from the database so that it can be bound to the user object.
	 *
	 * @param   integer  $userId  An optional user id.
	 * @param   boolean  $reset   False if row not found or on error
	 *                           (internal error state set in that case).
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function load($userId = null, $reset = true)
	{
		// Get the id to load.
		if ($userId !== null)
		{
			$this->id = $userId;
		}
		else
		{
			$userId = $this->id;
		}

		// Check for a valid id to load.
		if ($userId === null)
		{
			return false;
		}

		// Reset the table.
		$this->reset();

		// Load the user data.
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_db->quoteName('#__users'));
		$query->where($this->_db->quoteName('id') . ' = ' . (int) $userId);
		$this->_db->setQuery($query);
		$data = (array) $this->_db->loadAssoc();

		if (!count($data))
		{
			return false;
		}

		// Bind the data to the table.
		$return = $this->bind($data);

		return $return;
	}
}
