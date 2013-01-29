<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */

defined('_JEXEC') or die;

/**
 * The router class.
 *
 * @since  1.0
 */
class TadaApplicationRouter extends JApplicationWebRouter
{
	/**
	 * @var    array  An array of rules, each rule being an associative array('regex'=> $regex, 'vars' => $vars, 'controller' => $controller)
	 *                for routing the request.
	 * @since  1.0
	 */
	protected static $maps = array();

	/**
	 * @var    array  An array of HTTP Method => action in controller.
	 * @since  1.0
	 */
	protected static $methodMap = array(
		'GET' => 'index',
		'POST' => 'create',
		'PUT' => 'update',
		'DELETE' => 'delete'
	);

	/**
	 * Constructor.
	 *
	 * @param   JApplicationWeb  $app    The web application on whose behalf we are routing the request.
	 * @param   JInput           $input  An optional input object from which to derive the route.  If none
	 *                                   is given than the input from the application object will be used.
	 *
	 * @since   12.2
	 */
	public function __construct(JApplicationWeb $app, JInput $input = null)
	{
		$this->app   = $app;
		$this->input = ($input === null) ? $this->app->input : $input;
		$this->default = '/';
	}

	/**
	 * Add a route map to the router.  If the pattern already exists it will be overwritten.
	 *
	 * @param   string  $pattern     The route pattern to use for matching.
	 * @param   string  $controller  The controller name to map to the given pattern.
	 * @param   string  $action      The action name to map to the given pattern.
	 * @param   string  $method      The method to map to the given pattern.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	public static function addMap($pattern, $controller, $action, $method = 'GET')
	{
		// Sanitize and explode the pattern.
		$pattern = explode('/', trim(parse_url((string) $pattern, PHP_URL_PATH), ' /'));

		// Prepare the route variables
		$vars = array();

		// Initialize regular expression
		$regex = array();

		// Loop on each segment
		foreach ($pattern as $segment)
		{
			if ($segment == '')
			{
				$regex[] = '';
			}
			// Match a splat with no variable.
			elseif ($segment == '*')
			{
				$regex[] = '.*';
			}
			// Match a splat and capture the data to a named variable.
			elseif ($segment[0] == '*')
			{
				$vars[] = substr($segment, 1);
				$regex[] = '(.*)';
			}
			// Match an escaped splat segment.
			elseif ($segment[0] == '\\' && $segment[1] == '*')
			{
				$regex[] = '\*' . preg_quote(substr($segment, 2));
			}
			// Match an unnamed variable without capture.
			elseif ($segment == ':')
			{
				$regex[] = '[^/]*';
			}
			// Match a named variable and capture the data.
			elseif ($segment[0] == ':')
			{
				$vars[] = substr($segment, 1);
				$regex[] = '([^/]*)';
			}
			// Match a segment with an escaped variable character prefix.
			elseif ($segment[0] == '\\' && $segment[1] == ':')
			{
				$regex[] = preg_quote(substr($segment, 1));
			}
			// Match the standard segment.
			else
			{
				$regex[] = preg_quote($segment);
			}
		}

		array_push(
			self::$maps,
			array(
				'regex' => chr(1) . '^' . implode('/', $regex) . '$' . chr(1),
				'vars' => $vars,
				'controller' => (string) $controller,
				'action' => (string) $action,
				'method' => (string) $method
			)
		);
	}

	/**
	 * Map a resource as beeing restful.
	 *
	 * @param   string  $resource          The name of the resource.
	 * @param   string  $resource_path     The resource path.
	 * @param   string  $controller        The controller to map the resource.
	 * @param   string  $namespace_prefix  The namespace prefix to use.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	private static function addRESTfulResource($resource, $resource_path, $controller, $namespace_prefix)
	{
		$stringInflector = JStringInflector::getInstance();

		// Check if the resource is plural.
		if ($stringInflector->isPlural($resource))
		{
			self::addMap($resource_path, $controller, $namespace_prefix . 'index', 'GET');
		}

		self::addMap($resource_path . '/new', $controller, $namespace_prefix . 'new', 'GET');
		self::addMap($resource_path, $controller, $namespace_prefix . 'create', 'POST');
		self::addMap($resource_path . '/:id', $controller, $namespace_prefix . 'show', 'GET');
		self::addMap($resource_path . '/:id/edit', $controller, $namespace_prefix . 'edit', 'GET');
		self::addMap($resource_path . '/:id', $controller, $namespace_prefix . 'update', 'PUT');
		self::addMap($resource_path . '/:id', $controller, $namespace_prefix . 'delete', 'DELETE');
	}

	/**
	 * Public method to map a resource
	 *
	 * @param   string  $resource  The resource name.
	 * @param   array   $options   An array with specific options for the resource. controller, resources, namespace, members, collection.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	public static function mapResource($resource, $options = array())
	{
		self::addResource($resource, $options);
	}

	/**
	 * Method to add a resource to the map. It is called recursively until all the resources are mapped.
	 *
	 * @param   string  $resource          The resource name
	 * @param   string  $options           An array with specific options for the resource.  controller, resources, namespace, members, collection.
	 * @param   string  $path              The path of the resource. By default it is the root of the website
	 * @param   string  $namespace_prefix  The prefix of the namespace.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	private static function addResource($resource, $options = array(), $path = '/', $namespace_prefix = '')
	{
		// Get a string inflector.
		$stringInflector = JStringInflector::getInstance();

		// Check if there is a namespace.
		if (array_key_exists("namespace", $options))
		{
			if (empty($namespace_prefix))
			{
				$namespace_prefix = $options['namespace'] . '_';
			}
			else
			{
				$namespace_prefix = $namespace_prefix . $options['namespace'] . '_';
			}
			$namespace_path = '/' . $options['namespace'];
			$namespace = $options['namespace'];
		}
		else
		{
			$namespace_path = '';
			$namespace = '';
		}

		// Check if there is a controller specified.
		if (array_key_exists('controller', $options))
		{
			$controller = $options['controller'];
		}
		else
		{
			$controller = $resource;
		}

		// Build resource path.
		$resource_path = $path . $namespace_path . '/' . $resource;

		// Check if there are members for the current resource.
		if (array_key_exists('members', $options))
		{
			$members = $options['members'];
			foreach ($members as $member => $method)
			{
				self::addMap($resource_path . '/:id/' . $member, $controller, $namespace_prefix . $member, strtoupper($method));
			}
		}

		// Check if there are members for the current resource.
		if (array_key_exists('collections', $options))
		{
			$collections = $options['collections'];
			foreach ($collections as $collection => $method)
			{
				self::addMap($resource_path . '/' . $collection, $controller, $namespace_prefix . $collection, strtoupper($method));
			}
		}

		// Add RESTful routes for the resource.
		self::addRESTfulResource($resource, $resource_path, $controller, $namespace_prefix);

		// Check if there are nested resources. We limit them to only one nesting level.
		if (array_key_exists("resources", $options))
		{
			foreach ($options['resources'] as $c_resource => $c_options)
			{
				$c_resource_path = $resource_path . '/:' . $stringInflector->toSingular($resource) . '_id';

				self::addResource($c_resource, $c_options, $c_resource_path, $namespace_prefix);
			}
		}
	}

	/**
	 * Add a route map to the router.  If the pattern already exists it will be overwritten.
	 *
	 * @param   array  $maps  A list of route maps to add to the router as $pattern => $controller.
	 *
	 * @return  JApplicationWebRouter  This object for method chaining.
	 *
	 * @since   1.0
	 */
	public function addMaps($maps)
	{
		foreach ($maps as $pattern => $controller)
		{
			$this->addMap($pattern, $controller);
		}

		return $this;
	}

	/**
	 * Parse the given route and return the name of a controller mapped to the given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  string  The controller name for the given route excluding prefix.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function parseRoute($route)
	{
		$controller = false;
		$action = false;
		$params = array();

		// Trim the query string off.
		$route = preg_replace('/([^?]*).*/u', '\1', $route);

		// Sanitize and explode the route.
		$route = trim(parse_url($route, PHP_URL_PATH), ' /');

		// Get the REST method.
		$method = strtoupper($this->input->server->getMethod());
		if (strcmp($method, 'POST') == 0)
		{

			$postMethod = $this->input->get->getWord('_method');

			if (isset($postMethod))
			{
				$method = strtoupper($postMethod);
			}
		}

		// Iterate through all of the known route maps looking for a match.
		foreach (self::$maps as $rule)
		{
			if (preg_match($rule['regex'], $route, $matches) && strcmp(strtoupper($rule['method']), $method) == 0)
			{
				// If we have gotten this far then we have a positive match.
				$controller = $rule['controller'];
				$action = $rule['action'];

				// Time to set the input variables.
				// We are only going to set them if they don't already exist to avoid overwriting things.
				foreach ($rule['vars'] as $i => $var)
				{
					$params[$var] = $matches[$i + 1];
				}

				$this->input->def('_rawRoute', $route);

				break;
			}
		}

		// We were unable to find a route match for the request, redirect to the default route.
		if (!$controller)
		{
			throw new RuntimeException("Could not find route.", 400);
		}

		return array('controller' => $controller, 'action' => $action, 'params' => $params, 'method' => $method );
	}

	/**
	 * Find and execute the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function execute($route)
	{
		// Get the controller name based on the route patterns and requested route.
		$routeDetails = $this->parseRoute($route);

		// Set controller and action that will be used
		$this->input->def('_controller', $routeDetails['controller']);
		$this->input->def('_action', $routeDetails['action']);
		$this->input->def('_method', $routeDetails['method']);

		// Get the controller object by name.
		$controller = $this->getController($routeDetails['controller'], $routeDetails['params']);

		// Execute the controller.
		$controller->execute();
	}

	/**
	 * Get a JController object for a given name.
	 *
	 * @param   string  $name    The controller name (excluding prefix) for which to fetch and instance.
	 * @param   string  $params  The params to pass to the controller contructor.
	 *
	 * @return  JController
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	protected function getController($name, $params)
	{
		// Derive the controller class name.
		$class = $this->controllerPrefix . ucfirst($name) . 'Controller';

		// If the controller class does not exist panic.
		if (!class_exists($class) || !is_subclass_of($class, 'JController'))
		{
			throw new RuntimeException(sprintf('Unable to locate controller `%s`.', $class), 404);
		}

		// Instantiate the controller.
		$controller = new $class($this->input, $this->app, $params);

		return $controller;
	}
}
