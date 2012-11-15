<?php

/**
 * Constant that is checked in included files to prevent direct access.
 */
const _JEXEC = 1;

// Define the paths we need
const JPATH_ROOT = __DIR__;
define('JPATH_BASE', dirname(__DIR__));
define('JPATH_APP', JPATH_BASE . '/app');
define('JPATH_CODE', JPATH_BASE . '/lib/tada');
define('JPATH_CONTROLLERS', JPATH_APP . '/controller');
define('JPATH_MODELS', JPATH_APP . '/model');
define('JPATH_CONFIGURATION', JPATH_BASE . '/config');
define('JPATH_LIBRARIES', dirname(JPATH_BASE) . '/joomla-platform/libraries');

// Set up the environment
error_reporting(E_ALL);
ini_set('display_errors', true);
const JDEBUG = true;

// Import the Joomla Platform.
require_once JPATH_LIBRARIES . '/import.php';

//JLoader::registerPrefix('JMVC', JPATH_APP);
JLoader::registerPrefix('Tada', JPATH_CODE, true);
JLoader::discover('', JPATH_BASE, true, true);

// Get the application
$app = JApplicationWeb::getInstance('TadaApplicationWeb');

// Execute the application
$app->execute();