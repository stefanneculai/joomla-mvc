<?php
/**
 * An example configuration file for an application built on the Joomla Platform.
 *
 * This file will be automatically loaded by the webapplication.
 *
 * @package    Joomla.Examples
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Prevent direct access to this file outside of a calling application.
defined('_JEXEC') or die;

/**
 * Web configuration class.
 *
 * @package  Joomla.Examples
 * @since    11.3
 */
final class JConfig
{
	/**
	 * The database driver.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $db_driver = 'mysqli';

	/**
	 * Database host.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $db_host = 'localhost';

	/**
	 * The database connection user.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $db_user = 'root';

	/**
	 * The database connection password.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $db_password = 'password';

	/**
	 * The database name.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $db_name = 'jmvc';

	/**
	 * The database table prefix, if necessary.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $db_prefix = 'tada_';

	/**
	 * The session lifetime.
	 *
	 * @var    string
	 * @since  11.3
	 */

	public $lifetime = '15';

	/**
	 * The session handler.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $session_handler = 'database';
	/**
	 * The cache handler.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $cache_handler = 'file';
	/**
	 * The cache time.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $cachetime = '5';
	/**
	 * The application theme.
	 *
	 * @var    string
	 * @since  12.1
	 */
	public $theme = 'mt';
}
