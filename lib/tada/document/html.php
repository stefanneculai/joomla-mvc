<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.utilities.utility');

require_once JPATH_LIBRARIES . '/joomla/document/html/html.php';

/**
 * DocumentHTML class, provides an easy interface to parse and display a HTML document
 *
 * @package     Joomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class TadaDocumentHTML extends JDocumentHtml
{
	// The view object used to render files.
	public $view;

	public function element($name, $data = array())
	{
		return $this->view->element($name, $data);
	}
}
