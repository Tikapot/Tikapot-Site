<?php
/*
 * Tikapot Form Field
 *
 */

abstract class FormField
{
	protected $name, $value, $options, $error;
	
	public function __construct($name, $initial_value = "", $options = array()) {
		$this->name = $name;
		$this->value = $initial_value;
		$this->options = $options;
		$this->error = "";
	}
	
	public function validate($base_id, $safe_name) {
		return true;
	}
	
	public function set_name($val) {
		$this->name = $val;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function set_value($val) {
		$this->value = $val;
	}
	
	public function get_value() {
		return $this->value;
	}
	
	public function get_type() { return ""; }
	
	public function set_error($val) {
		$this->error = $val;
	}
	
	public function get_error() {
		return $this->error;
	}
	
	protected function get_placeholder() {
		return isset($this->options['placeholder']) ? $this->options['placeholder'] : "";
	}
	
	protected function get_extras() {
		return isset($this->options['extra']) ? $this->options['extra'] : "";
	}
	
	public function get_error_html($base_id, $safe_name) {
		$field_id = $this->get_field_id($base_id, $safe_name);
		if (strlen($this->error) > 0)
			return '<label for="'.$field_id.'">'.$this->error.'</label>';
		return '';
	}
	
	public function get_field_id($base_id, $safe_name) {
		return $base_id . '_' . $safe_name;
	}
	
	
	public function get_label($base_id, $safe_name) {
		$field_id = $this->get_field_id($base_id, $safe_name);
		if ($this->get_type() !== "hidden")
			return '<label for="'.$field_id.'">'.$this->name.'</label>';
		return '';
	}
	
	
	public function get_input($base_id, $safe_name) {
		$ret = "";
		$field_id = $this->get_field_id($base_id, $safe_name);
		$ret .= '<input';
		if ($base_id !== "control")
			$ret .= ' id="'.$field_id.'"';
		$ret .= ' class="'.$safe_name.'_field" type="'.$this->get_type().'" name="'.$field_id.'" value="'.$this->value.'"';
		if ($this->get_placeholder() !== "")
			$ret .= ' placeholder="'.$this->get_placeholder().'"';
		if ($this->get_extras() !== "")
			$ret .= ' ' . $this->get_extras();
		$ret .= ' />';
		return $ret;
	}
}
?>

