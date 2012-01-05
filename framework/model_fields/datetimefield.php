<?php
/*
 * Tikapot DateTime Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");
require_once(home_dir . "framework/model_fields/datefield.php");

class DateTimeField extends DateField
{
	public static $TIME_FORMAT = "H:i:s";
	public static $FORMAT = "Y-m-d H:i:s";
	protected static $db_type = "timestamp";
	
	public function get_formfield($name) {
		return new DateTimeFormField($name, $this->get_value());
	}
	
	public function get_time() {
		return date(DateTimeField::$TIME_FORMAT, strtotime($this->value));
	}
	
	public function validate() {
		if (strlen($this->value) == 0)
			return True;
		$regex = "/^(\d{4})(-)(\d{2})(-)(\d{2})\x20(\d{2})(:)(\d{2})(:)(\d{2})$/";
		$valid = preg_match($regex, $this->value) == 1;
		if (!$valid)
			array_push($this->errors, $GLOBALS["i18n"]["fielderr4"]);
		return $valid;
	}
}

?>

