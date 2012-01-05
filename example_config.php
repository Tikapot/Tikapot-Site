<?php
define("debug", true);
define("debug_show_queries", false);

/* Tikapot Options */
$tp_options = array(
	"default_i18n" => "en",
	
	/* The following are used by TPCache */
	"enable_cache" => true,
	"cache_prefix" => "tp",
	
	/* The following are used by the auth app */
	"password_salt" => "c8227cny8c3y287ym78ym87y2m783c2ym", // Make this unique
	"session_timeout" => 60 * 60 * 4,                       // 4 hours
	
	/* The following are used by the Media Manager */
	"media_dir" => home_dir . "media/",
	"media_url" => home_url . "media/",
);

/* Databases */
$databases = array(
	"default" => array(
		"type" => "psql",
		"host" => "localhost",
		"port" => "",
		"name" => "",
		"username" => "",
		"password" => "",
		"timeout" => "5"
	)
);

/* Memcached */
$caches = array(
	"default" => array(
		"host" => "localhost",
		"port" => 11211
	)
);

$app_paths = array("apps", "contrib", "tests");
$apps_list = array("example");

date_default_timezone_set("Europe/London");
?>

