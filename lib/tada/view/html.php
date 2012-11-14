<?php

defined('_JEXEC') or die;

class TadaViewHtml extends JViewHtml
{
	protected $_viewVars = array();

	public function __construct(TadaModel $model, SplPriorityQueue $paths = null)
	{
		parent::__construct($model, $paths);
	}

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  RuntimeException
	 */
	public function render()
	{
		return parent::render();
	}

	public function element($name, $data = array())
	{
		// Set paths.
		$paths = new SplPriorityQueue;

		$pathsCopy = clone($this->paths);
		$pathsCopy->top();
		while($pathsCopy->valid())
		{
			// Insert elements path
			$paths->insert($pathsCopy->current() . '/../elements/', 'normal');
			$pathsCopy->next();
		}

		// Set element layout.
		$element = new TadaViewHtml(new TadaModel, $paths);
		$element->setLayout($name);

		// Set vars in view
		foreach($this->_viewVars as $key => $value)
		{
			$element->{$key} = $value;
		}

		return $element->render();
	}

	public function __set($name, $value)
	{
		$this->_viewVars[$name] = $value;
    }

	public function __get($name)
	{
		if(array_key_exists($name, $this->_viewVars))
			return $this->_viewVars[$name];
		else
			return null;
    }
}