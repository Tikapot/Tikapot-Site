<?php
/*
 * Tikapot Request Class
 *
 */

require_once(home_dir . "framework/i18n.php");

class Request
{
	public $method, $page, $get, $post, $cookies, $mimeType, $messages;
	
	public function __construct() {
		$this->method = "GET";
		if (count($_POST) > 0)
			$this->method = "POST";
		$this->get = $_GET;
		$this->post = $_POST;
		$this->vars = $_REQUEST;
		$this->cookies = $_COOKIE;
		$this->page = "/";
		if (isset($this->get[page_def])) {
			$this->page = trim($this->get[page_def]);
		}
		if (isset($this->page[0]) && $this->page[0] == '/') {
			$this->page = substr($this->page, 1);
		}
		$this->fullPath = home_url . $this->page;
		$this->page = '/' . $this->page;
		$this->mimeType = $this->get_mime_type($this->page);
		if (isset($this->get[page_def]))
			unset($this->get[page_def]);
		$this->visitor_ip = $this->getIP();
		$this->messages = isset($_SESSION['request_messages']) ? $_SESSION['request_messages'] : array();
		$this->init_i18n();
	}
	
	private function init_i18n() {	
		if (isset($this->get['langswitch'])) {
			$_SESSION['lang'] = $this->get['langswitch'];
			$file = isset($_SESSION['lang']) ? $_SESSION['lang'] : "en";
			$filename = home_dir . "i18n/" . $file . ".php";
			if (!strpos($file, "..") && file_exists($filename))
				require($filename);
			else
				require(home_dir . "i18n/en.php");
			$GLOBALS["i18n"] = $i18n_data;
		}
		
		$this->i18n = new i18n($GLOBALS["i18n"]);
	}
	
	/*
	 * Messaging framework for requests
	 */
	public function message($message) {
		$this->messages[] = $message;
		$_SESSION['request_messages'] = $this->messages;
	}
	
	public function delete_messages() {
		$this->messages = array();
		$_SESSION['request_messages'] = array();
	}
	
	public function print_messages() {
		print '<div class="messages">';
		foreach ($this->messages as $message) {
			print '<div class="message"><p>' . $message . '</p></div>';
		}
		print '</div>';
	}
	
	public function print_and_delete_messages() {
		$this->print_messages();
		$this->delete_messages();
	}
	
	public function getFullPath() {
		return $this->fullPath;
	}
	
	public function getIP() {
		if (strlen(getenv("HTTP_CLIENT_IP")) > 0)
			return getenv("HTTP_CLIENT_IP");
		if (strlen(getenv("HTTP_X_FORWARDED_FOR")) > 0)
			return getenv("HTTP_X_FORWARDED_FOR");
		if (strlen(getenv("REMOTE_ADDR")) > 0)
			return getenv("REMOTE_ADDR");
		return "UNKNOWN";
	}
	
	function get_mime_type($filename) {
		if (!file_exists($filename) || is_dir($filename))
			return "text/html";
		$fileext = substr(strrchr($filename, '.'), 1);
		switch ($fileext) {
			case "css":
				return "text/css";
			case "js":
				return "text/javascript";
			default:
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$type = finfo_file($finfo, $filename);
				finfo_close($finfo);
				return $type;
		}
	}
}

?>

