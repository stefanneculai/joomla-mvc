<?php
defined('_JEXEC') or die;

abstract class TadaController extends JControllerBase
{
	// The name of this controller.
	protected $name;

	// The name of the currently requested controller action.
	protected $action;

	// The vars that will be passed to the view.
	protected $viewVars = array();

	// The params of the action.
	protected $params = array();

	// The data of the action
	protected $data = array();

	public function __construct(JInput $input = null, JApplicationBase $app = null, $params = array(), $data = array())
	{
		// Setup dependencies.
		$this->app = isset($app) ? $app : $this->loadApplication();
		$this->input = isset($input) ? $input : $this->loadInput();

		// Get the name of this controller.
		$this->name = isset($this->name) ? $this->name : $this->input->get('_controller');

		// Get the currently requested action.
		$this->action = $this->input->get('_action');

		// Set the params passed to the controller.
		$this->params = array_merge($params, $this->input->get->getArray());

		// Get POST data.
		$this->data = $this->input->post->getArray();

		// Load model.
		$this->loadModel();
	}

	private function loadModel()
	{
		// Get a string inflector.
		$stringInflector = JStringInflector::getInstance();

		// Build model class name.
		$modelClass = ucfirst($stringInflector->toSingular($this->name));

		// Check if model class exists.
		if (!class_exists($modelClass) || !is_subclass_of($modelClass, 'TadaModel'))
		{
			throw new RuntimeException(sprintf('Unable to locate model `%s`.', $class), 404);
		}

		// Set model in controller.
		$this->{ucfirst($stringInflector->toSingular($this->name))} = new $modelClass;
	}

	public function execute()
	{
		// Call the action.
		$this->{$this->action}();

		// Get document.
		$doc = $this->app->getDocument();

		// Set paths.
		$paths = new SplPriorityQueue;
		// Themed path.
		if(isset($this->theme))
		{
			$paths->insert(JPATH_APP . '/view/' . $this->theme .'/' . $this->input->get('_controller'), 'normal');
			$this->app->set('theme', $this->theme);
		}
		// Default path.
		$paths->insert(JPATH_APP . '/view/default/' . $this->input->get('_controller'), 'normal');

		// Set view layout.
		$view = new TadaViewHtml(new TadaModel, $paths);
		$view->setLayout($this->input->get('_action'));

		// Set vars in view
		foreach($this->viewVars as $key => $value)
		{
			$view->{$key} = $value;
		}

		$doc->setBuffer($view->render(), 'content');
	}

	public function __set($name, $value)
	{
		$this->viewVars[$name] = $value;
    }
}