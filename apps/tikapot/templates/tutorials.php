<?php
$title = $request->i18n['tp_tutorials'] . " | Tikapot";
include("includes/header.php");
?>

<a href="<?php echo home_url; ?>"><img src="<?php echo home_url; ?>apps/tikapot/media/images/logo.png" alt="Tikapot Logo" id="page_logo" /></a>
<ul class="menu breadcrumbs">
	<li><a href="<?php echo home_url; ?>"><?php echo $request->i18n['tp_home']; ?></a> &raquo;</li>
	<li><?php echo $request->i18n['tp_tutorials']; ?></li>
</ul>
<div id="page">
	<h1 id="tutorial_title"><?php echo $request->i18n['tp_tutorials']; ?></h1>
	<div id="tutorial_description">
		<p><?php echo $request->i18n['tutorials_desc']; ?></p>
		<ol>
		<?php
			for($i = 1; $i <= 4/*9*/; $i++)
				echo '<li><a href="'.home_url.'tutorials/'.$request->i18n['tutorial'.$i].'/" class="tutclick" data-id="'.$i.'">'.$request->i18n['tutorial'.$i].'</a></li>';
		?>
		</ol>
	</div>
</div>
<script type="text/javascript" src="<?php echo home_url; ?>apps/tikapot/media/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo home_url; ?>tikapot/i18n.js"></script>
<script type="text/javascript">
$(function(){
	$(".tutclick").click(function() {
		var id = $(this).attr("data-id");
		$(".breadcrumbs").html('<li><a href="<?php echo home_url; ?>"><?php echo $request->i18n['tp_home']; ?></a> &raquo;</li><li><a href="<?php echo home_url; ?>tutorials/"><?php echo $request->i18n['tp_tutorials']; ?></a> &raquo;</li><li>'+i18n["tutorial"+id]+'</li>');
		$("#tutorial_title").html(i18n["tutorial"+id]);
		$("#tutorial_description").html(i18n["tutorial"+id+"d"]);
		return false;
	});
});
</script>
<?php
include("includes/footer.php");
?>
