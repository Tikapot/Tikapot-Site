<?php
/*
 * Tikapot Version 1.0
 * 
 * For installation instructions see README
 * For license information please see LICENSE
 */

define("site_version", '1.0');
define("page_def", 'tpage');
define("home_dir", dirname(__FILE__) . '/');
define("home_url", substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/") + 1));

require_once(home_dir . "config.php");

if (debug)
	ini_set('display_errors', '1');

require_once(home_dir . "framework/init.php");
?>

