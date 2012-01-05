<?php
/*
 * Tikapot Framework models
 *
 */

require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "framework/utils.php");

class ContentType extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField(150));
	}
	
	public function obtain() {
		return get_named_class($this->name);
	}
	
	public static function of($obj) {
		static $ctypes = array();
		$class = get_class($obj);
		if (!isset($ctypes[$class])) {
			list($obj, $created) = ContentType::get_or_create(array("name"=>$class));
			$ctypes[$class] = $obj->pk;
		}
		return $ctypes[$class];
	}
}
?>
