<?php
/*
 * Tikapot Forms
 *
 */


require_once(home_dir . "framework/form_fields/init.php");
require_once(home_dir . "framework/form_printers.php");
require_once(home_dir . "framework/fieldset.php");
require_once(home_dir . "framework/utils.php");

class FormException extends Exception {}

class Form
{
	protected $action, $method, $form_id, $fieldsets = array(), $errors = array(), $model = false;
	
	/*
	Data can be a model or an array of form fields
	*/
	public function __construct($data, $action = "", $method = "POST", $overrides = "") {
		$this->form_id = (isset($_SESSION["current_form_id"]) ? $_SESSION["current_form_id"] + 1 : 1);
		$_SESSION["current_form_id"] = $this->form_id;
		$this->action = $action;
		$this->method = $method;
		$this->generate_control_block();
		if (is_array($data)) {
			$this->load_fields($data);
		} else {
			$this->load_model($data);
		}
		if ($overrides !== "")
			$this->load_fields($overrides);
	}
	
	public function get_header() {
		$header = '<form';
		$header .= ' id="'.$this->get_form_id().'"';
		$header .= ' action="'.$this->action.'"';
		$header .= ' method="'.$this->method.'"';
		if ($this->has_file())
			$header .= ' enctype="multipart/form-data"';
		$header .= '>';
		return $header;
	}
	
	public function has_file() {
		foreach($this->fieldsets as $name => $fieldset) {
			foreach($fieldset as $field_name => $field) {
				if ($field->get_type() == "file")
					return true;
			}
		}
		return false;
	}
	
	public function get_form_id() {
		return "form_" . $this->form_id;
	}
	
	public function get_fieldsets() {
		return $this->fieldsets;
	}
	
	public function get_value($name) {
		foreach($this->fieldsets as $i => $fields)
			foreach ($fields as $fname => $field)
				if ($fname === $name)
					return $field->get_value();
		return false;
	}
	
	protected function check_csrf($form_id, $token) {
		return isset($_SESSION[$form_id]) && isset($_SESSION[$form_id]["csrf"]) && $_SESSION[$form_id]["csrf"] == $token;
	}
	
	protected function generate_control_block() {
		$this->fieldsets["control"] = new Fieldset("", array(), "control");
		// CSRF token field
		list($id, $csrf) = $this->generate_csrf_token();
		$this->fieldsets["control"]["formid"] = new HiddenFormField("formid", $id);
		$this->fieldsets["control"]["csrf"] = new HiddenFormField("csrf", $csrf);
	}
	
	protected function generate_csrf_token() {
		$form_key = $this->get_form_id();
		$token = md5(uniqid(rand(), true));
		if (!isset($_SESSION[$form_key]))
			$_SESSION[$form_key] = array();
		$_SESSION[$form_key]["csrf"] = $token;
		return array($form_key, $token);
	}
	
	public function validate() {
		$result = true;
		foreach($this->fieldsets as $i => $fields) {
			$fid = $fields->get_id($this->get_form_id());
			foreach ($fields as $fname => $field)
				$result = $result && $field->validate($fid, $fname);
		}
		return $result;
	}
	
	public function load_post_data($data) {
		if (!isset($data["control_formid"]) || !isset($data["control_csrf"]))
			throw new FormException($GLOBALS["i18n"]["formerrctrl"]);
		if (!$this->check_csrf($data["control_formid"], $data["control_csrf"]))
			throw new FormException($GLOBALS["i18n"]["formerrcsrf"]);
	
		// Work out the form key
		list($l, $m, $r) = partition($data['control_formid'], "_");
		$this->form_id = $r;
		$key = "form_" . $this->form_id . "_";
		$key_len = strlen($key);
		
		// Rebuild Control block
		$this->fieldsets["control"]["formid"]->set_value("form_" . $this->form_id); 
		list($id, $csrf) = $this->generate_csrf_token();
		$this->fieldsets["control"]["csrf"]->set_value($csrf); 
		
		// Re-Construct Data
		$fieldset = $this->fieldsets["0"];
		foreach($data as $name => $value) {
			if($name == "submit" || starts_with($name, "control_"))
				continue;
			$field = substr($name, $key_len);
			foreach($this->fieldsets as $ti => $tfields)
				foreach ($tfields as $tname => $tfield)
					if ($tname === $field)
						$fieldset = $tfields;
			
			if (!isset($fieldset[$field]))
				throw new FormException($GLOBALS["i18n"]["formerrdata"] . $name);
			$fieldset[$field]->set_value($value); 
		}
		
		return $this->validate();
	}
	
	public function clear_data() {
		foreach($this->fieldsets as $name => $fieldset) {
			if($name == "control")
				continue;
			foreach($fieldset as $field_name => $field) {
				$field->set_value("");
			}
		}
	}
	
	protected function load_fields($fieldsets) {
		foreach ($fieldsets as $i => $fields) {
			if(!is_array($fields)) {
				$this->fieldsets["".$i] = $fields;
				continue;
			}
			$this->fieldsets["".$i] = new Fieldset();
			foreach ($fields as $name => $field) {
				$this->fieldsets["".$i][$name] = $field;
			}
		}
	}
	
	protected function add_modelfield($fieldset, $name, $field) {
		if (!isset($this->fieldsets[$fieldset]))
			$this->fieldsets[$fieldset] = new Fieldset();
		$this->fieldsets[$fieldset][$name] = $field->get_formfield($name);
	}
	
	protected function load_model($model) {
		$this->model = $model;
		if ($model->fromDB())
			$this->fieldsets["control"]["modelid"] = new HiddenFormField("modelid", $model->pk);
		$this->fieldsets["control"]["modelct"] = new HiddenFormField("modelct", $model->get_content_type());
		$fields = $model->get_fields();
		foreach ($fields as $name => $field) {
			$this->add_modelfield("0", $name, $field);
		}
	}
	
	public function save($model = false) {
		if ($model === false)
			$model = $this->model;
		if (!$model)
			throw new Exception($GLOBALS["i18n"]["formerrsave"]);
		
		foreach ($this->fieldsets as $fieldset => $fields) {
			if ($fieldset != "control") {
				foreach ($fields as $name => $field) {
					try {
						$mfield = $model->get_field($name);
						$mfield->set_value($field->get_value());
					} catch(Exception $e) {
					}
				}
			}
		}
		$model->save();
		return $model;
	}
	
	public function display($printer = null) {
		if ($printer === null)
			$printer = new HTMLFormPrinter();
		$printer->run($this);
	}
}
?>

