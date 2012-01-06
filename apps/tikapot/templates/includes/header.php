<!doctype html> 
<html lang="en">
<head> 
  <meta charset="utf-8">
 
  <title><?php print $title; ?></title> 
  <meta name="description" content="Tikapot"> 
  <meta name="author" content="Tikapot">
  
  <?php
  // Get Media Manager to handle our CSS
  include_once(home_dir . "framework/media.php");
  $manager = new MediaManager("style" . site_version, home_dir . "apps/tikapot/media/", home_url . "apps/tikapot/media/");
  $manager->add_file(home_dir . "apps/tikapot/media/css/style.css");
  
  print '<link rel="stylesheet" href="'.$manager->build_css().'" type="text/css" media="screen" />';
  ?>
</head> 
 
<body>
	<div id="content">
		<div id="content_header">
			<!--<a href="https://github.com/Tikapot/Tikapot/tarball/master"><?php echo $request->i18n['tpdownload']; ?></a>-->
		</div>
