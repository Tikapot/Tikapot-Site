<?php
/*
 * Tikapot View System
 *
 */

class View
{
	protected $url, $page;
	
	public function __construct($url, $page = "") {
		$this->set_url($url);
		$this->page = $page;
		
		global $view_manager;
		$view_manager->add($this);
	}
	
	public function set_url($url) {
		$this->url = $url;
	}
	
	public function get_url() {
		return $this->url;
	}
	
	/* Override me */ 
	public function setup($request, $args = array()) {
		return true;
	}
	 
	/* Request is a 'Request' object. By default this simply includes $this->page be sure to override for more complex things! */
	public function render($request, $args = array()) {
		include($this->page);
	}
}

?>

