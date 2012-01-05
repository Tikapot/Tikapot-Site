<?php
/*
 * Tikapot Foreign Key Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");
require_once(home_dir . "framework/utils.php");

class FKValidationException extends Exception { }

class FKField extends ModelField
{
	protected static $db_type = "varchar"; // Beacuse the FK could be any type :(
	private $_obj, $valid = false, $_model, $_class, $_override_db_type;
	
	public function __construct($model, $override_db_type = null) {
		parent::__construct();
		$this->_obj = Null;
		$this->_model = $model;
		$this->determine_class();
		$this->_override_db_type = $override_db_type;
	}
	
	public function get_formfield($name) {
		return new HiddenFormField($name, $this->get_value());
	}
	
	public function is_set() {
		return $this->value !== 0 && isset($this->_obj);
	}
	
	private function determine_class() {
		/*
		 * Class is in the format: appname.modelName
		 * We must scan app paths for the app, then import models.py.
		 * Hopefully, modelName willl then exist
		 */
		list($app, $n, $class) = partition($this->_model, '.');
		if (!class_exists($class)) {
			global $app_paths;
			$test_paths = $app_paths;
			if (!in_array("framework", $test_paths))
				$test_paths[] = "framework";
			foreach ($test_paths as $app_path) {
				$path = home_dir . $app_path;
				if ($app !== "framework")
					$path .= '/' . $app;
				$path .= "/models.php";
				if (is_file($path)) {
					include($path);
					break;
				}
			}
		}
		if (class_exists($class)) {
			$this->valid = true;
			$this->_class = $class;
			return;
		}
		throw new FKValidationException($GLOBALS["i18n"]["error1"] . " '" . $this->_model . "' " . $GLOBALS["i18n"]["fielderr5"]);
	}
	
	private function grab_object() {
		if (strlen($this->value) > 0) {
			try {
				return call_user_func(array($this->_class, 'get'), array("pk" => $this->value));
			} catch (Exception $e) {
				return null;
			}
		}
		return new $this->_class();
	}
	
	private function check_obj() {
		if ($this->valid && $this->_obj === Null)
			$this->_obj = $this->grab_object();
	}
	
	public function set_value($value) {
		parent::set_value($value);
		if (strlen($value) > 0)
			$this->_obj = $this->grab_object();
	}
	
	public function get_value() {
		return $this->is_set() ? $this->get_object() : false;
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val == NULL) ? $this->value : $val;
		return (strlen("" . $val)  > 0) ? $db->escape_string($val) : "0";
	}
	
	public function get_object() {
		$this->check_obj();
		return $this->_obj;
	}
	
	public function get_class() {
		return $this->_class;
	}
	
	public function __toString() {
		return (isset($this->value)) ? "" . $this->value : "";
	}
	
	public function get_db_type() {
		if ($this->_override_db_type !== null)
			return $this->_override_db_type;
		$db_type = static::$db_type;
		if ($this->_class) {
			$obj = new $this->_class();
			$db_type = $obj->get_field("pk")->get_db_type();
		}
		return $db_type;
	}
	
	/* This recieves pre-save signal from it's model. */
	public function pre_save($model, $update) {
		// Save our model and set this db value to it's ID
		if ($this->is_set()) {
			$this->_obj->save();
			$this->value = $this->_obj->pk;
		}
	}
	
	public function __get($name) {
		$this->check_obj();
		if ($this->valid && isset($this->_obj->$name))
			return $this->_obj->$name;
	}
	
	public function __set($name, $value) {
		$this->check_obj();
		if ($this->valid && isset($this->_obj->$name))
			$this->_obj->$name = $value;
	}
	
	public function __call($name, $args) {
		$this->check_obj();
		if($this->valid && method_exists($this->_obj, $name)) {
			call_user_func_array(array($this->_obj, $name), $args);
		}
	}
	
	public function __isset($name) {
		$this->check_obj();
		return $this->valid && isset($this->_obj->$name);
	}
	
	public function __unset($name) {
		$this->check_obj();
		if ($this->valid)
			unset($this->_obj->$name);
	}
	
	public function validate() {
		return $this->valid;
	}
}

?>

