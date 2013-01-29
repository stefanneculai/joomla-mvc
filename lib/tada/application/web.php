<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */
defined('_JEXEC') or die;

/**
 * Application Web class.
 *
 * @since  1.0
 */
class TadaApplicationWeb extends JApplicationWeb
{
	// The router of the application.
	protected $router;

	/**
	 * Class constructor.
	 *
	 * @param   mixed  $input   An optional argument to provide dependency injection for the application's
	 *                          input object.  If the argument is a JInput object that object will become
	 *                          the application's input object, otherwise a default input object is created.
	 * @param   mixed  $config  An optional argument to provide dependency injection for the application's
	 *                          config object.  If the argument is a JRegistry object that object will become
	 *                          the application's config object, otherwise a default config object is created.
	 * @param   mixed  $client  An optional argument to provide dependency injection for the application's
	 *                          client object.  If the argument is a JApplicationWebClient object that object will become
	 *                          the application's client object, otherwise a default client object is created.
	 */
	public function __construct(JInput $input = null, JRegistry $config = null, JApplicationWebClient $client = null)
	{
		parent::__construct($input, $config, $client);

		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData(JPATH_CONFIG . '/configuration.php'));

		// Inject the application into JFactory.
		JFactory::$application = $this;

		// Load router.
		$this->loadRouter();

		// Do not load DB for the moment.
		$this->loadDatabase();

		// Set session.
		$this->loadSession();
	}

	/**
	 * Allows the application to load a custom or default router.
	 *
	 * @param   WebServiceApplicationWebRouter  $router  An optional router object. If omitted, the standard router is created.
	 *
	 * @return  JApplicationWeb This method is chainable.
	 *
	 * @since   1.0
	 */
	public function loadRouter(TadaApplicationRouter $router = null)
	{
		// Load a new router.
		$this->router = (is_null($router)) ? new TadaApplicationRouter($this, $this->input) : $router;

		require JPATH_CONFIG . '/routes.php';

		return $this;
	}

	/**
	 * Method to run the Web application routines.
	 *
	 * @return  void
	 */
	protected function doExecute()
	{
		// Load document and save it in factory.
		$this->loadDocument();
		$document = $this->getDocument();

		// Inject the document object into the factory.
		JFactory::$document = $document;

		// Set page title.
		$document->setTitle($this->get('app_title'), 'Tada MVC App');

		// Register the default layout to the config.
		$this->set('theme', $this->get('app_theme', ''));
		$this->set('themes.base', JPATH_APP . '/view/layouts');
		$this->set('themeFile',  $this->get('app_layout', 'index.php'));
		$this->set('themeParams', new JRegistry);

		// Execute router.
		$this->router->execute($this->get('uri.route'));
	}

	/**
	 * Load document for the MVC.
	 *
	 * @param   JDocument  $document  A JDocument object.s
	 *
	 * @return  void
	 */
	public function loadDocument(JDocument $document = null)
	{
		if ($document !== null)
		{
			$this->document = $document;
		}
		else
		{
			if (JFactory::$document)
			{
				return JFactory::$document;
			}

			$lang = JFactory::getLanguage();

			$type = $this->input->get('format', 'html', 'word');

			$attributes = array('charset' => 'utf-8', 'lineend' => 'unix', 'tab' => '  ', 'language' => $lang->getTag(),
							'direction' => $lang->isRTL() ? 'rtl' : 'ltr');

			$signature = serialize(array($type, $attributes));

			$instance = new TadaDocumentHTML($attributes);

			$instance->setType($type);

			$this->document = $instance;
		}
	}

	/**
	 * Allows the application to load a custom or default database driver.
	 *
	 * @param   JDatabaseDriver  $driver  An optional database driver object. If omitted, the application driver is created.
	 *
	 * @return  JApplicationBase This method is chainable.
	 *
	 * @since   1.0
	 */
	public function loadDatabase(JDatabaseDriver $driver = null)
	{
		// Check if no driver was passed.
		if (is_null($driver))
		{
			$this->db = JDatabaseDriver::getInstance(
				array(
					'driver' => $this->get('db_driver'),
					'host' => $this->get('db_host'),
					'user' => $this->get('db_user'),
					'password' => $this->get('db_pass'),
					'database' => $this->get('db_name'),
					'prefix' => $this->get('db_prefix')
				)
			);

			// Select the database.
			$this->db->select($this->get('db_name'));
		}

		// Use the given database driver object.
		else
		{
			$this->db = $driver;
		}

		// Set the database to our static cache.
		JFactory::$database = $this->db;

		return $this;
	}
}
