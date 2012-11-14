<?php
defined('_JEXEC') or die;

abstract class TinyController extends JControllerBase
{
	public function execute()
	{
		$app = $this->getApplication();
		$doc = $app->getDocument();

		$paths = new SplPriorityQueue;
		if(isset($this->theme))
		{
			$paths->insert(JPATH_APP . '/view/' . $this->theme .'/' . $this->input->get('_controller'), 'normal');
			$app->set('theme', $this->theme);
		}
		$paths->insert(JPATH_APP . '/view/default/' . $this->input->get('_controller'), 'normal');

		$view = new TinyViewHtml(new TinyModel, $paths);
		$view->setLayout($this->input->get('_action'));

		$doc->setBuffer($view->render(), 'content');
	}
}