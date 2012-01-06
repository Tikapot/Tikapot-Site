<?php
$title = $request->i18n['tp_home'] . " | Tikapot";
include("includes/header.php");
?>

<a href="<?php echo home_url; ?>"><img src="<?php echo home_url; ?>apps/tikapot/media/images/logo.png" alt="Tikapot Logo" /></a>
<h1><?php echo $request->i18n['welcometp']; ?></h1>
<p><?php echo $request->i18n['welcometp_desc']; ?></p>
<ul class="menu">
	<li><a href="https://github.com/downloads/Tikapot/Tikapot/Tikapot1.0.tar.gz">&raquo; <?php echo $request->i18n['tpdownload']; ?> &laquo;</a></li>
</ul>
<div class="pad1">
	<p><?php echo $request->i18n['welcometp_desc2']; ?></p>
	<ul class="menu">
		<li><a href="<?php echo home_url; ?>tutorials/"><?php echo $request->i18n['tutorials']; ?></a></li>
<?php if (false) { ?>
		<li><a href="<?php echo home_url; ?>app-designer/"><?php echo $request->i18n['appdes']; ?></a></li>
<?php } ?>
	</ul>
</div>
<div class="pad1">
	<p><?php echo $request->i18n['welcometp_desc3']; ?></p>
	<ul class="menu">
		<li><a href="<?php echo home_url; ?>features/"><?php echo $request->i18n['features']; ?></a></li>
		<li><a href="<?php echo home_url; ?>marzipan/"><?php echo $request->i18n['planned']; ?></a></li>
<?php if (false) { ?>
		<li><a href="<?php echo home_url; ?>showcase/"><?php echo $request->i18n['showcase']; ?></a></li>
<?php } ?>
	</ul>
</div>
<?php
include("includes/footer.php");
?>
