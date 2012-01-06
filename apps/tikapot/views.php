<?php
require_once(home_dir . "framework/view.php");

class TutorialView extends View
{
	public function setup($request, $args) {
		$tutorial = $args['tutorial'];
		foreach($request->i18n as $key => $val) {
			if ($val == $tutorial) {
				$request->tutorial = $key;
				break;
			}
		}
		return isset($request->tutorial);
	}
}
?>

