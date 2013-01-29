<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.utilities.utility');

require_once JPATH_LIBRARIES . '/joomla/document/html/html.php';

/**
 * The Document class.
 *
 * @since  1.0
 */
class TadaDocumentHTML extends JDocumentHtml
{
	// The view object used to render files.
	public $view;

	/**
	 * Render an element.
	 *
	 * @param   string  $name  Element name.
	 * @param   array   $data  An array with data to pass to the element.
	 *
	 * @since   1.0
	 * @return  mixed
	 */
	public function element($name, $data = array())
	{
		return $this->view->element($name, $data);
	}
}
