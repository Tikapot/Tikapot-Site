<?php
/*
 * Tikapot Cron Manager
 * 
 * This file should be run every minute
 *
 */
ini_set('display_errors', '1');

define("home_dir", realpath('../..') . '/');
require_once(home_dir . "config.php");
require_once(home_dir . "contrib/cron/models.php");

foreach ($apps_list as $app) {
	list($obj, $created) = CronStore::get_or_create(array("app_name" => $app));
	if ($obj->locked)
		continue;
		
	// Search
	$app_cron_file = "";
	foreach ($app_paths as $app_path) {
		$filename = home_dir . $app_path . "/" . $app . "/cron.php";
		if (file_exists($filename)) {
			$app_cron_file = $filename;
			break;
		}
	}
	
	// Execute
	if (strlen($app_cron_file) > 0) {
		// Does the file need running?
		$run = strlen($obj->last_run) == 0;
		if (!$run) {
			$content = file($app_cron_file);
			$matches = false;
			foreach($content as $line) {
				preg_match('/\@cron_time: (?P<period>(\d|\*|\s)+)/', $line, $matches);
				if ($matches)
					break;
			}
			if (!$matches) {
				print "Error running cron '" . $app . "': File has no time period comment!\n";
				continue;
			}
			$period = $matches["period"];
			$run = true; // For now - todo: decide based on above period
		}
		if ($run) {
			// Obtain lock
			$obj->locked = True;
			$obj->save();
			
			@include($app_cron_file);
	
			$obj->last_run = date("Y-m-d H:m:s");
	
			// Release lock
			$obj->locked = False;
			$obj->save();
		}
	}
}

?>

