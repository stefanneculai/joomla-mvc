<?php

defined('_JEXEC') or die;

class TadaViewHtml extends JViewHtml
{
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

	}
}