<?php
/*
 * Tikapot redirect View
 *
 */

require_once(home_dir . "framework/view.php");

class CaptchaView extends View
{
	public function setup($request, $args) {
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-type: image/jpeg');
		$this->width = isset($request->get['width']) ? $request->get['width'] : 200;
		$this->height = isset($request->get['height']) ? $request->get['height'] : 100;
		return true;
	}
	
	public function render($request, $args) {
		// Create an image
		$image = @imagecreatetruecolor($this->width, $this->height) or die("Error");
		$background = imagecolorallocate($image, 0, 0, 0);
		imagefill($image, 0, 0, $background);
		$textcolor = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
		imagestring($image, 5, 2, 2, $_SESSION["captcha"][$request->get['sesid']], $textcolor);
		imagejpeg($image, NULL, 20);
		imagedestroy($image);
	}
}

class CaptchaVerificationView extends View
{
	public function render($request, $args) {
		$ses_key = $request->get['sesid'];
		$captcha_key = $request->get['key'];
		if ($_SESSION["captcha"][$request->get['sesid']] == $captcha_key)
			print '1';
		else
			print '0';
	}
}
?>
