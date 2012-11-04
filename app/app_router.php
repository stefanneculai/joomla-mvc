<?php
defined('_JEXEC') or die;

class AppRouter extends JApplicationWebRouter
{
	/**
	 * @var    array  An array of rules, each rule being an associative array('regex'=> $regex, 'vars' => $vars, 'controller' => $controller)
	 *                for routing the request.
	 * @since  12.2
	 */
	protected $maps = array();

	/**
	 * @var    array  An array of HTTP Method => action in controller.
	 * @since  12.2
	 */
	protected $methodMap = array(
		'GET' => 'index',
		'POST' => 'create',
		'PUT' => 'update',
		'DELETE' => 'delete'
	);

	/**
	 * Add a route map to the router.  If the pattern already exists it will be overwritten.
	 *
	 * @param   string  $pattern     The route pattern to use for matching.
	 * @param   string  $controller  The controller name to map to the given pattern.
	 *
	 * @return  JApplicationWebRouter  This object for method chaining.
	 *
	 * @since   12.2
	 */
	public function addMap($pattern, $controller, $action, $method = 'GET')
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
			// Match a splat with no variable.
			if ($segment == '*')
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

		$this->maps[] = array(
			'regex' => chr(1) . '^' . implode('/', $regex) . '$' . chr(1),
			'vars' => $vars,
			'controller' => (string) $controller,
			'action' => (string) $action,
			'method' => (string) $method
		);

		return $this;
	}

	/**
	 *
	 * @param unknown_type $resource
	 * @param unknown_type $options  controller, in_resource, namespace, // TODO add members, add collection
	 */
	public function addResource($resource, $options = array())
	{
		// Get a string inflector.
		$stringInflector = JStringInflector::getInstance();

		// Check if this is a nested resource.
		if (array_key_exists("in_resource", $options))
		{
			$in_resource_path = '/' . $options['in_resource'] . '/:' . $stringInflector->toSingular($options['in_resource']) . '_id';
		}
		else
		{
			$in_resource_path = '';
		}

		// Check if there is a namespace.
		if (array_key_exists("namespace", $options))
		{
			$namespace_prefix = $options['namespace'] . '_';
			$namespace = $options['namespace'];
		}
		else
		{
			$namespace_prefix = '';
			$namespace = '';
		}

		if (array_key_exists('controller', $options))
		{
			$controller = $options['controller'];
		}
		else
		{
			$controller = $resource;
		}

		// Build resource path
		$resource_path =  $namespace  . $in_resource_path . '/' . $resource;


		// Check if the resource is plural
		if ($stringInflector->isPlural($resource)) {
			$this->addMap($resource_path, $controller, $namespace_prefix . 'index', 'GET');
		}

		$this->addMap($resource_path . '/new', $controller, $namespace_prefix . 'new', 'GET');
		$this->addMap($resource_path, $controller, $namespace_prefix . 'create', 'POST');
		$this->addMap($resource_path . '/:id', $controller, $namespace_prefix . 'show', 'GET');
		$this->addMap($resource_path . '/:id/edit', $controller, $namespace_prefix . 'edit', 'GET');
		$this->addMap($resource_path . '/:id', $controller, $namespace_prefix . 'update', 'PUT');
		$this->addMap($resource_path . '/:id', $controller, $namespace_prefix . 'delete', 'DELETE');

	}

	/**
	 * Add a route map to the router.  If the pattern already exists it will be overwritten.
	 *
	 * @param   array  $maps  A list of route maps to add to the router as $pattern => $controller.
	 *
	 * @return  JApplicationWebRouter  This object for method chaining.
	 *
	 * @since   12.2
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
	 * @since   12.2
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

		// If the route is empty then simply return the default route.  No parsing necessary.
		if ($route == '')
		{
			return $this->default;
		}

		// Iterate through all of the known route maps looking for a match.
		foreach ($this->maps as $rule)
		{
			if (preg_match($rule['regex'], $route, $matches) && strcmp(strtoupper($rule['method']), strtoupper($this->input->server->getMethod())) == 0)
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

		// We were unable to find a route match for the request.  Panic.
		if (!$controller)
		{
			throw new InvalidArgumentException(sprintf('Unable to handle request for route `%s`.', $route), 404);
		}

		return array('controller' => $controller, 'action' => $action, 'params' => $params);
	}

	/**
	 * Find and execute the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function execute($route)
	{
		// Get the controller name based on the route patterns and requested route.
		$routeDetails = $this->parseRoute($route);

		print_r($routeDetails);
		die();

		// Get the controller object by name.
		$controller = $this->fetchController($name);

		// Execute the controller.
		$controller->execute();
	}
}