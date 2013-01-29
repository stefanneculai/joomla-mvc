<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */

defined('_JEXEC') or die;

/**
 * The view class.
 *
 * @since  1.0
 */
class TadaViewHtml extends JViewHtml
{
	// The vars inside view.
	private $_viewVars = array();

	/**
	 * The constructor
	 *
	 * @param   TadaModel         $model  The model object.
	 * @param   SplPriorityQueue  $paths  A priority queue.
	 *
	 * @since   1.0
	 */
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

	/**
	 * Method to render an element.
	 *
	 * @param   string  $name  The element name.
	 * @param   array   $data  The data to pass to the element.
	 *
	 * @since   1.0
	 *
	 * @return  mixed
	 */
	public function element($name, $data = array())
	{
		// Set paths.
		$paths = new SplPriorityQueue;

		$pathsCopy = clone($this->paths);
		$pathsCopy->top();
		while ($pathsCopy->valid())
		{
			// Insert elements path
			$paths->insert($pathsCopy->current() . '/../elements/', 'normal');
			$pathsCopy->next();
		}

		// Set element layout.
		$element = new TadaViewHtml(new TadaModel, $paths);
		$element->setLayout($name);

		// Set vars in view
		foreach ($this->_viewVars as $key => $value)
		{
			$element->{$key} = $value;
		}

		foreach ($data as $key => $value)
		{
			$element->{$key} = $value;
		}

		return $element->render();
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
		$this->_viewVars[$name] = $value;
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
		if (array_key_exists($name, $this->_viewVars))
		{
			return $this->_viewVars[$name];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Helper method to get the val of an array element if it exists or empty otherwise.
	 *
	 * @param   string  $key    The key to search by.
	 * @param   array   $array  The array.
	 *
	 * @since   1.0
	 * @return  string
	 */
	public function val($key, $array)
	{
		if ($this->{$array})
		{
			if (array_key_exists($key, $this->{$array}))
			{
				return $this->{$array}[$key];
			}
			else
			{
				return '';
			}
		}
		else
		{
			return '';
		}
	}
}
