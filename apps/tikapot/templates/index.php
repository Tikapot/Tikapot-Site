<?php
$title = $request->i18n['tp_home'] . " | Tikapot";
include("includes/header.php");
?>

<a href="<?php echo home_url; ?>"><img src="<?php echo home_url; ?>apps/tikapot/media/images/logo.png" alt="Tikapot Logo" /></a>
<h1><?php echo $request->i18n['welcometp']; ?></h1>
<p><?php echo $request->i18n['welcometp_desc']; ?></p>
<ul class="menu">
	<li>
		<a href="https://github.com/Tikapot/Tikapot/tarball/master">&raquo; <?php echo $request->i18n['tpdownload']; ?> &laquo;</a>
	</li>
</ul>
<div class="pad1">
	<p><?php echo $request->i18n['welcometp_desc2']; ?></p>
	<ul class="menu">
		<li>
			<a href="<?php echo home_url; ?>tutorials/">Tutorials</a>
		</li>
		<li>
			<a href="<?php echo home_url; ?>app-designer/">App Designer</a>
		</li>
	</ul>
</div>
<div class="pad1">
	<p><?php echo $request->i18n['welcometp_desc3']; ?></p>
	<ul class="menu">
		<li>
			<a href="<?php echo home_url; ?>showcase/">Showcase</a>
		</li>
	</ul>
</div>
<?php
include("includes/footer.php");
?>
