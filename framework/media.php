<?php
/*
 * Tikapot Media File Handler
 *
 */

require_once(home_dir . "framework/utils.php");
require_once(home_dir . "lib/jsmin.php");

class MediaManager
{
	private $media_files = array(), $media_key = "", $media_dir = "", $media_url = "";
	
	public function __construct($media_key = "", $dir_override = "", $url_override = "") {
		$this->media_key = $media_key;
		
		global $tp_options;
		$this->media_dir = isset($tp_options['media_dir']) ? $tp_options['media_dir'] : "";
		$this->media_dir = strlen($dir_override) > 0 ? $dir_override : $this->media_dir;
		$this->media_url = isset($tp_options['media_url']) ? $tp_options['media_url'] : "";
		$this->media_url = strlen($url_override) > 0 ? $url_override : $this->media_url;
	}
	
	public function get_media_dir() {
		return $this->media_dir;
	}
	
	public function get_media_url() {
		return $this->media_url;
	}
	
	public function add_file($file) {
		$this->media_files[] = $file;
	}
	
	public function build($ext) {
		if (strlen($this->media_key) > 0) {
			$filename = $this->get_media_dir() . "cache/" . $this->media_key . "." . $ext;
			if (file_exists($filename))
				return $this->get_media_url() . "cache/" . $this->media_key . "." . $ext;
		}
		$data = "";
		foreach($this->media_files as $key => $file) {
			if (ends_with($file, $ext)) {
				$data .= "\n" . file_get_contents($file);
			}
		}
				
		// Minify
		switch ($ext) {
			case "css":
				$data = preg_replace('/\n\s*\n/',"\n", $data);
				$data = preg_replace('!/\*.*?\*/!s','', $data);
				$data = preg_replace('/[\n\t]/',' ', $data);
				$data = preg_replace('/ +/',' ', $data);
				$data = preg_replace('/ ?([,:;{}]) ?/','$1',$data);
				break;
			case "js":
				$data = JSMin::minify($data);
				break;
		}
		
		if (strlen($key) == 0)
			$key = md5($data);
		$filename = $this->get_media_dir() . "cache/" . $this->media_key . "." . $ext;
		if (!file_exists($filename)) {
			if (file_put_contents($filename, trim($data)) !== FALSE)
				return $this->get_media_url() . "cache/" . $this->media_key . "." . $ext;
		} else {
			return $this->get_media_url() . "cache/" . $this->media_key . "." . $ext;
		}
		return false;
	}
	
	public function build_css() {
		return $this->build("css");
	}
	
	public function build_js() {
		return $this->build("js");
	}
}

?>
