<?php

defined('_JEXEC') or die;

class TinyViewHtml extends JViewHtml
{
	public function __construct(TinyModel $model, SplPriorityQueue $paths = null)
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
}