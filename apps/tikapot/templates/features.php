<?php
$title = $request->i18n['tp_features'] . " | Tikapot";
include("includes/header.php");
?>

<a href="<?php echo home_url; ?>"><img src="<?php echo home_url; ?>apps/tikapot/media/images/logo.png" alt="Tikapot Logo" id="page_logo" /></a>
<ul class="menu breadcrumbs">
	<li><a href="<?php echo home_url; ?>"><?php echo $request->i18n['tp_home']; ?></a> &raquo;</li>
	<li><?php echo $request->i18n['tp_features']; ?></li>
</ul>
<div id="page">
	<h1><?php echo $request->i18n['tp_features']; ?></h1>
	<div>
		<?php echo $request->i18n["features_list"]; ?>
	</div>
</div>
<?php
include("includes/footer.php");
?>
