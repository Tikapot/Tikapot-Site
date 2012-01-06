<?php
require_once(home_dir . "apps/tikapot/views.php");
require_once(home_dir . "framework/view.php");

new View("/", home_dir . "apps/tikapot/templates/index.php");
new View("/tutorials/", home_dir . "apps/tikapot/templates/tutorials.php");
new View("/app-designer/", home_dir . "apps/tikapot/templates/appdesigner.php");
new View("/showcase/", home_dir . "apps/tikapot/templates/showcase.php");
new View("/features/", home_dir . "apps/tikapot/templates/features.php");
new View("/marzipan/", home_dir . "apps/tikapot/templates/marzipan.php");
new TutorialView("/tutorials/(?P<tutorial>[\w[:punct:]\s]+)/", home_dir . "apps/tikapot/templates/tutorial.php");
?>

