<?php
defined('_JEXEC') or die;

abstract class TinyController extends JControllerBase
{
	public function __construct($model, JInput $input = null, JApplicationBase $app = null)
	{
		// Setup dependencies.
		$this->app = isset($app) ? $app : $this->loadApplication();
		$this->input = isset($input) ? $input : $this->loadInput();

		// Load model.
		$this->loadModel($model);
	}

	private function loadModel($model)
	{
		// Build model class name.
		$modelClass = ucfirst($model);

		// Check if model class exists.
		if (!class_exists($modelClass) || !is_subclass_of($modelClass, 'TinyModel'))
		{
			throw new RuntimeException(sprintf('Unable to locate model `%s`.', $class), 404);
		}

		// Set model in controller.
		$this->{$model} = new $modelClass;
	}

	public function execute()
	{
		// Get document.
		$doc = $this->app->getDocument();

		// Set paths.
		$paths = new SplPriorityQueue;
		// Themed path.
		if(isset($this->theme))
		{
			$paths->insert(JPATH_APP . '/view/' . $this->theme .'/' . $this->input->get('_controller'), 'normal');
			$app->set('theme', $this->theme);
		}
		// Default path.
		$paths->insert(JPATH_APP . '/view/default/' . $this->input->get('_controller'), 'normal');

		// Set view layout.
		$view = new TinyViewHtml(new TinyModel, $paths);
		$view->setLayout($this->input->get('_action'));

		$doc->setBuffer($view->render(), 'content');
	}
}