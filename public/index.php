<?php

/**
 * Constant that is checked in included files to prevent direct access.
 */
const _JEXEC = 1;

// Define the paths we need
const JPATH_ROOT = __DIR__;
define('JPATH_BASE', dirname(__DIR__));
define('JPATH_APP', JPATH_BASE . '/app');
define('JPATH_CONTROLLERS', JPATH_APP . '/controllers');
define('JPATH_MODELS', JPATH_APP . '/models');
define('JPATH_CONFIG',    JPATH_BASE . '/config');
define('JPATH_LIBRARIES', dirname(JPATH_BASE) . '/joomla-platform/libraries');

// Set up the environment
error_reporting(E_ALL);
ini_set('display_errors', true);
const JDEBUG = true;

// Import the Joomla Platform.
require_once JPATH_LIBRARIES . '/import.php';
require_once JPATH_APP . '/loader.php';

// Load app files
AppLoader::discover('', JPATH_APP);

// Load controller files
AppLoader::discover('', JPATH_CONTROLLERS);

// Load model files
AppLoader::discover('', JPATH_MODELS);

// Get the application
$app = JApplicationWeb::getInstance('AppWeb');

// Execute the application
$app->execute();