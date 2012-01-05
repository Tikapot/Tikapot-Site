<?php
/*
 * Tikapot i18n View
 *
 */

require_once(home_dir . "framework/view.php");

class i18nJSView extends View
{
	public function __construct() {
		parent::__construct("/contrib/i18n.js");
	}
	
	public function render($request) {
		header('Content-type: text/javascript');
		print( $request->i18n->buildJS() );
	}
}
?>

