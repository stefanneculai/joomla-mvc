<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */

/**
 * A few helper methods to validate data.
 *
 * @since  1.0
 */
class TadaHelperForm
{
	/**
	 * Validate an email string
	 *
	 * @param   string  $email  The email string.
	 *
	 * @since   1.0
	 *
	 * @return  boolean
 	 */
	public static function valid_email($email)
	{
		$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
		if (!preg_match($regex, $email))
		{
			return false;
		}

		return true;
	}

	/**
	 * Validate the password
	 *
	 * @param   string  $password  The password string.
	 *
	 * @since   1.0
	 *
	 * @return  boolean
 	 */
	public static function valid_password($password)
	{
		if (strlen($password) < 8)
		{
			return false;
		}

		return true;
	}
}
