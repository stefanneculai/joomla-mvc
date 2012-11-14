<?php
defined('_JEXEC') or die;

class TadaApplicationWeb extends JApplicationWeb
{
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

		$this->config->set('session', false);


		// Inject the application into JFactory
		JFactory::$application = $this;

		// Load router
		$this->loadRouter();

		// Load database
		// $this->setUpDB();
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

		//TODO Add routes to the router. Read them from file
		$this->router->mapResource('books', array(
					'members' => array('preview' => 'GET'),
					'collections' => array('search' => 'GET'),
					'resources'=> array('photos' => array('namespace' => 'admin', 'members' => array('test' => 'post')))));

		// Add a restful singular resource to router.
		$this->router->mapResource('test');

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

		// Inject the document object into the factory
		JFactory::$document = $document;

		// Set page title.
		$document->setTitle('Tada MVC');

		// Register the template to the config
		$this->set('theme', 'default');
		$this->set('themes.base', JPATH_APP . '/view');
		$this->set('themeParams', new JRegistry);

		// Execute router
		$this->router->execute($this->get('uri.route'));
	}
}