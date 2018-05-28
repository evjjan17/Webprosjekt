<?php
/*
  Plugin Name: CM Maps Routes Manager
  Plugin URI: https://www.cminds.com/
  Description: Allow users to draw routes and to generate a catalog of map routes and trails
  Author: CreativeMindsSolutions
  Version: 2.7.9
 */

if (version_compare('5.3', PHP_VERSION, '>')) {
	die(sprintf('We are sorry, but you need to have at least PHP 5.3 to run this plugin (currently installed version: %s)'
		. ' - please upgrade or contact your system administrator.', PHP_VERSION));
}

require_once dirname(__FILE__) . '/App.php';
com\cminds\mapsroutesmanager\App::bootstrap(__FILE__);

