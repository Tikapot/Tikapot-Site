<?php
/*
 * Tikapot Timer App
 *
 */

require_once(home_dir . "contrib/timer/timer.php");

global $signal_manager;

function start_page_timer($request) {
	$request->pagetimer = Timer::start();
}

$signal_manager->hook("page_load_start", "start_page_timer");
?>

