<?php
/*
 * Tikapot Core Framework
 *
 */
ob_start();
@session_start();

/* Setup i18n */
global $tp_options;
$file = isset($_SESSION['lang']) ? $_SESSION['lang'] : $tp_options['default_i18n'];
@setlocale(LC_ALL, $file);
$filename = home_dir . "i18n/" . $file . ".php";
if (!strpos($file, "..") && file_exists($filename))
	require($filename);
else
	require(home_dir . "i18n/en.php");
$GLOBALS["i18n"] = $i18n_data;

/* Start up the signal manager, register some signals */
require_once(home_dir . "framework/signal_manager.php");
$signal_manager = new SignalManager();
$signal_manager->register("page_load_start", "page_load_setup", "page_load_render", "page_load_setup_failure", "page_load_end");

/* Start up the view manager */
require_once(home_dir . "framework/view_manager.php");
$view_manager = new ViewManager();
require_once(home_dir . "framework/urls.php");

/* Load the apps */
global $app_paths, $apps_list;
foreach ($apps_list as $app) {
	foreach ($app_paths as $app_path) {
		$filename = home_dir . $app_path . "/" . $app . "/init.php";
		if (file_exists($filename)) {
			include($filename);
			break;
		}
	}
}

/* Create the request */
require_once(home_dir . "framework/request.php");
$request = new Request();

/* Setup the page */
header('Content-type: ' . $request->mimeType);

/* Render the page */
$signal_manager->fire("page_load_setup", $request);
$signal_manager->fire("page_load_start", $request);
$page = "";
if ($view_manager->setup($request)) {
	$signal_manager->fire("page_load_render", $request);
	ob_start();
	$view_manager->render($request);
	$page = ob_get_clean();
} else {
	$signal_manager->fire("page_load_setup_failure", $request);
}
$signal_manager->fire("page_load_end", $request);
$script_output = ob_get_clean();

print $page;
if (debug) {
	print $script_output;
}
?>
