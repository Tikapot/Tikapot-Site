<?php
/*
 * Tikapot Char Form Field
 *
 */

require_once(home_dir . "framework/form_fields/formfield.php");

class CharFormField extends FormField
{
	public function get_type() {
		return "text";
	}
}

class CaptachaField extends CharFormField
{
	private $image_location, $image_url, $width, $height;
	
	public function __construct($name, $initval = "", $options = array()) {
		parent::__construct($name, $initval, $options);
		$this->width = isset($options['width']) ? $options['width'] : 200;
		$this->height = isset($options['height']) ? $options['height'] : 100;
	}
	
	private function get_string($length = 7) { 
		$rand_src = array(array(48,57), array(97,122)); 
		srand((double) microtime() * 245167413); 
		$random_string = ""; 
		for($i = 0; $i < $length; $i++){ 
			$i1 = rand(0, sizeof($rand_src) - 1); 
			$random_string .= chr(rand($rand_src[$i1][0], $rand_src[$i1][1])); 
		} 
		return $random_string; 
	}
	
	public function validate($base_id, $safe_name) {
		$id = $this->get_field_id($base_id, $safe_name);
		if(isset($_SESSION["captcha"][$id]) && $this->get_value() == $_SESSION["captcha"][$id])
			return true;
		$this->set_error($GLOBALS["i18n"]["captchaerr"]);
		return false;
	}
	
	public function get_image($base_id, $safe_name) {
		$id = $this->get_field_id($base_id, $safe_name);
		if(!isset($_SESSION["captcha"]) || !is_array($_SESSION["captcha"]))
			$_SESSION["captcha"] = array();
		if (!isset($_SESSION["captcha"][$id]))
			$_SESSION["captcha"][$id] = $this->get_string(7);
		return '<img src="'.home_url.'tikapot/api/captcha/?sesid='.$id.'&width='.$this->width.'&height='.$this->height.'" alt="CAPTCHA image" />';
	}
	
	public function get_raw_input($base_id, $safe_name) {
		return parent::get_input($base_id, $safe_name);
	}
	
	public function get_input($base_id, $safe_name) {
		// Return an image
		return $this->get_image($base_id, $safe_name) . '<br />' . $this->get_raw_input($base_id, $safe_name) ;
	}
}

class HiddenFormField extends FormField
{
	public function get_type() {
		return "hidden";
	}
}

class PasswordField extends FormField
{
	public function get_type() {
		return "password";
	}
}

class FileUploadField extends FormField
{
	public function get_type() {
		return "file";
	}
}

class TextFormField extends FormField
{
	public function get_input($base_id, $safe_name) {
		$field_id = $this->get_field_id($base_id, $safe_name);
		$field = '<textarea id="'.$field_id.'" name="'.$field_id.'" class="'.$safe_name.'_field"';
		if ($this->get_placeholder() !== "")
			$field .= ' placeholder="'.$this->get_placeholder().'"';
		if ($this->get_extras() !== "")
			$field .= ' ' . $this->get_extras();
		$field .= '>';
		$field .= $this->value;
		$field .= '</textarea>';
		return $field;
	}
}

class NumberField extends FormField
{
	public function get_type() {
		return "number";
	}
}

class URLFormField extends FormField
{
	public function get_type() {
		return "url";
	}
}

class TelephoneFormField extends FormField
{
	public function get_type() {
		return "tel";
	}
}

class EmailFormField extends FormField
{
	public function get_type() {
		return "email";
	}
}

class DateFormField extends FormField
{
	public function get_type() {
		return "date";
	}
}

class DateTimeFormField extends FormField
{
	public function get_type() {
		return "datetime";
	}
}

class SearchFormField extends FormField
{
	public function get_type() {
		return "search";
	}
}
?>

