<?php
/*
 * Tikapot Core Models
 *
 */

require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");

class Config extends Model
{	
	public function __construct() {
		parent::__construct();
		$this->add_field("key", new CharField("", 250));
		$this->add_field("value", new CharField("", 250));
	}
}

?>

