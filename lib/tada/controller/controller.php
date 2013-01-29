<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */
defined('_JEXEC') or die;

/**
 * The controller class.
 *
 * @since  1.0
 */
abstract class TadaController extends JControllerBase
{
	// The name of this controller.
	protected $name;

	// The name of the currently requested controller action.
	protected $action;

	// The vars that will be passed to the view.
	protected $viewVars = array();

	// The used theme.
	protected $theme = '';

	/**
	 * Check if user is authenticated. If it is not authenticated, redirect to the sign_in page.
	 *
	 * @param   string  $redirect  The path of the sign_in page. By default it is /sign_in
	 *
	 * @since   1.0
	 *
	 * @return  boolean
	 */
	protected function isAuth($redirect = '/sign_in')
	{
		$user = $this->app->getSession()->get('user');

		if (!($user instanceof JUser) || $user->guest == 1 )
		{
			if (!is_null($redirect))
			{
				// Redirect to login page.
				$this->app->redirect($redirect);
			}
			return false;
		}
		else
		{
			// Check if user account wasn't blocked.
			$cUser = new JUser($user->id);

			if (!($cUser instanceof JUser) || $cUser->guest == 1 || $cUser->block == 1)
			{
				// Delete user from session
				$this->app->getSession()->set('user', null);

				// Delete remember me cookie.
				setcookie(md5('IXAPI_REMEMBER'), "", time() - 3600, '/sign_in', '');

				if ($redirect == true)
				{
					// Redirect to the blocked page.
					$this->app->redirect('/blocked');
				}
				return false;
			}

			$this->app->getSession()->set('user', $cUser);
			return true;
		}
	}

	/**
	 * If there is a stored user in database, simply load it.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	protected function loadStored()
	{
		// Check if there is a remember cookie set
		$cookie_path = '/';
		$rCookie = $this->input->cookie->getString(md5(JFactory::getConfig()->get('app_title') . '_REMEMBER'));

		if (isset($rCookie))
		{
			// Check if there is any used in database with the current remember cookie.
			$mUser = new User;
			$usersByParam = $mUser->find(array('params' => sha1($rCookie . $cookie_path)));
			if (!empty($usersByParam))
			{
				// Build the current remember cookie value of the user.
				$controlToken = $this->rememberCookie($usersByParam[0]);

				// Remember cookie is valid.
				if ($rCookie == $controlToken)
				{
					// Load user in session.
					$this->app->getSession()->set('user', new JUser($usersByParam[0]->id));
				}
			}
		}
	}

	/**
	 * Generate the remember cookie token.
	 *
	 * @param   JUser  $user  An user object or null if current user should be used.
	 *
	 * @since   1.0
	 *
	 * @return  string
	 */
	protected function rememberCookie($user)
	{
		// Build credentials object.
		$credentials = array();
		$credentials['user'] = $user->email;
		$credentials['password'] = $user->password;
		$credentials['block'] = $user->block;
		$credentials['activation'] = $user->activation;
		$credentials['reset_token'] = $user->reset_token;

		// Create the encryption key, apply extra hardening using the user agent string.
		$privateKey = md5(@$_SERVER['HTTP_USER_AGENT']);

		$key = new JCryptKey('simple', $privateKey, $privateKey);
		$crypt = new JCrypt(new JCryptCipherSimple, $key);
		$rCookie = $crypt->encrypt(serialize($credentials));

		return $rCookie;
	}

	/**
	 * The contructor of the controller.
	 *
	 * @param   JInput            $input   The input of the application.
	 * @param   JApplicationBase  $app     A way to inject an application.
	 * @param   array             $params  Parameters passed to controller.
	 * @param   array             $data    Data passed to controller. It is passed by POST requests.
	 *
	 * @since   1.0
	 */
	public function __construct(JInput $input = null, JApplicationBase $app = null, $params = array(), $data = array())
	{
		JUser::getTable('user', 'TadaTable');

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

		// Set theme.
		$this->theme = $this->app->get('theme');

		// Set method
		$this->method = $this->input->get('_method');

		// Load model.
		$this->loadModel();
	}

	/**
	 * Load model.
	 *
	 * @throws  RuntimeException
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	private function loadModel()
	{
		// Get a string inflector.
		$stringInflector = JStringInflector::getInstance();

		// Build model class name.
		$modelClass = ucfirst($stringInflector->toSingular($this->name));

		// Check if model class exists.
		if (!class_exists($modelClass) || !is_subclass_of($modelClass, 'JModelBase'))
		{
			throw new RuntimeException(sprintf('Unable to locate model `%s`.', $modelClass), 404);
		}

		// Set model in controller.
		$this->{ucfirst($stringInflector->toSingular($this->name))} = new $modelClass;
	}

	/**
	 * Execute controller.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->loadStored();

		// Call the action.
		$this->{$this->action}();

		// Is authenticated.
		$this->isAuthenticated = $this->isAuth(null);

		// Get document.
		$doc = $this->app->getDocument();

		// Set paths.
		$paths = new SplPriorityQueue;

		// Themed path.
		if (!empty($this->theme))
		{
			$paths->insert(JPATH_APP . '/view/themes/' . $this->theme . '/' . $this->input->get('_controller'), 'normal');
			$this->app->set('themes.base', JPATH_APP . '/view/themes/' . $this->theme . '/layouts');
			$this->app->set('theme', '');
		}
		// Default path.
		$paths->insert(JPATH_APP . '/view/' . $this->input->get('_controller'), 'normal');

		if ($this->layout != null)
		{
			$this->app->set('themeFile', $this->layout . '.php');
		}

		// Set view layout.
		$view = new TadaViewHtml(new TadaModel, $paths);
		$view->setLayout($this->input->get('_action'));

		// Set vars in view
		foreach ($this->viewVars as $key => $value)
		{
			$view->{$key} = $value;
		}

		$view->data = $this->data;
		$view->params = $this->params;

		$doc->view = $view;

		$doc->setBuffer($view->render(), 'content');
	}

	/**
	 * Send an email.
	 *
	 * @param   string  $layout   The email layout.
	 * @param   string  $to       The to of the email.
	 * @param   string  $subject  The subject of the email.
	 * @param   array   $headers  Additional headers to add to the email. These are same as the ones from PHP officeial documentation for mail function.
	 *
	 * @since   1.0
	 * @return  void
	 */
	protected function email($layout, $to, $subject, $headers = array())
	{
		// Set the default headers.
		$m_headers = empty($headers) ? array ('MIME-Version' => '1.0',
							'Content-type' => 'text/html; charset=iso-8859-1',
							'From' => JFactory::$application->get('email_from')) : $headers;

		$headers = '';
		foreach ($m_headers as $header => $h_content)
		{
			if ($h_content != end($m_headers))
			{
				$headers .= $header . ': ' . $h_content . "\r\n";
			}
			else
			{
				$headers .= $header . ': ' . $h_content;
			}
		}

		// Set paths.
		$paths = new SplPriorityQueue;

		// Default path.
		$paths->insert(JPATH_APP . '/view/layouts/email/', 'normal');

		// Set view layout.
		$mail = new TadaViewHtml(new TadaModel, $paths);
		$mail->setLayout($layout);

		// Set vars in view
		foreach ($this->viewVars as $key => $value)
		{
			$mail->{$key} = $value;
		}

		$mail->data = $this->data;
		$mail->params = $this->params;

		mail($to, $subject, $mail->render(), $headers);
	}

	/**
	 * Setter for view vars.
	 *
	 * @param   string  $name   The name of the var.
	 * @param   mixed   $value  The value of the var.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	public function __set($name, $value)
	{
		$this->viewVars[$name] = $value;
	}

	/**
     * Getter for view vars.
     *
     * @param   string  $name  The name of the var.
     *
     * @since   1.0
     * @return  multitype:|NULL
     */
	public function __get($name)
	{
		if (array_key_exists($name, $this->viewVars))
		{
			return $this->viewVars[$name];
		}

		return null;
	}
}
