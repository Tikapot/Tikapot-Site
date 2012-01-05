<?php
/*
 * Tikapot Boolean Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");

class BooleanField extends ModelField
{
	protected static $db_type = "boolean";
	
	public function __construct($default = false) {
		parent::__construct($default);
	}
	
	public function get_value() {
		$val = strtolower($this->value);
		return ($val == 'true' || $val == "t" || $val == "1");
	}
	
	public function get_formfield($name) {
		return new CharFormField($name, $this->get_value());
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val == NULL) ? $this->get_value() : $val;
		$val = strlen($val) > 0 ? $val : $this->default_value;
		$val = ($val === true || $val == 'true' || $val == "t" || $val == "1"); // Convert to bool
		return ($val) ? "true" : "false";
	}

	public function validate() {
		$valid = $this->value === true || $this->value === false;
		if (!$valid)
			array_push($this->errors, $GLOBALS["i18n"]["fielderr1"] . " " . $this->get_value());
		return $valid;
	}
}

?>

