<?php
/*
 * Tikapot Model System
 *
 */

require_once(home_dir . "framework/database.php");
require_once(home_dir . "framework/model_query.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/utils.php");

class ValidationException extends Exception { }
class TableValidationException extends ValidationException { }
class ModelExistsException extends Exception {}
class FieldException extends Exception {}

abstract class Model
{
	private $from_db = False, $_valid_model = False;
	protected $fields = array(), $safe_fields = array(), $errors = array(), $_using = "default";
	
	public function __construct() {
		$this->add_field("id", new PKField(0, $max_length = 22, True));
		$this->_valid_model = True;
	}
	
	public function __toString() {
		return $this->pk;
	}
	
	public static function get_content_type() {
		return ContentType::of(static::get_temp_instance());
	}
	
	public function set_db($db) {
		$this->_using = $db;
	}
	
	public function fromDB() {
		return $this->from_db;
	}
	
	public static function get_temp_instance() {
		$obj = new static();
		$obj->_valid_model = False;
		return $obj;
	}
	
	public static function objects() {
		return new ModelQuery(static::get_temp_instance());
	}
	
	/* Allows custom primary keys */
	protected function _pk() {
		foreach ($this->fields as $name => $field)
			if ($field->is_pk_field())
				return $name;
	}
	
	public function get_pk_name() { return $this->_pk(); }
	
	/* Format: array("COL"=>"VAL") */
	public function load_values($array) {
		foreach ($this->fields as $name => $field)
			if (array_key_exists($name, $array)) {
				$val = $array[$name];
				if (is_array($val))
					$val = $val[0];
				$field->set_value($val);
			}
	}
	
	/* Load field values from query result. Sets "from_db" to True */
	public function load_query_values($result) {
		$this->load_values($result);
		$this->from_db = True;
	}
	
	// Allows access to stored models
	// Returns a modelquery object containing the elements
	// $query should be in the following format: (COL => Val, COL => (val, OPER), etc)
	public static function find($query) {
		return static::objects()->find($query);
	}
	
	// Allows access to stored models
	// Returns a single object
	// Errors if multiple objects are found or no objects are found
	// Arg can be an id or an array with multiple parameters
	public static function get($arg = 0) {
		$results = NULL;
    		if (is_array($arg))
			$results = static::find($arg);
		else
			$results = static::find(array("pk" => $arg));
		if ($results->count() == 0)
			throw new ModelExistsException($GLOBALS["i18n"]["noobjexist"]);
		if ($results->count() > 1)
			throw new ModelQueryException($GLOBALS["i18n"]["multiobjexist"]);
		return $results->get(0);
	}
	
	public static function create($args = array()) {
		if (count($args) <= 0)
			return Null;
		try {
			$obj = new static();
			$obj->load_values($args);
			$obj->save();
			return $obj;
		} catch (Exception $e) {
			throw new ModelQueryException($GLOBALS["i18n"]["error1"] . $e->getMessage());
		}
		return Null;
	}

	// Allows access to stored models
	// Arg can be an id or an array with multiple search parameters
	// Returns an array containing:  (a single object [creates it if needed], a boolean specifying weather or not the object is a new object)
	public static function get_or_create($args = 0) {
		$obj = NULL;
		$created = False;
		try {
			$obj = static::get($args);
		}
		catch (ModelExistsException $e) {
			$obj = static::create($args);
			$created = True;
		}
		return array($obj, $created);
	}
	
	public static function get_or_ignore($args) {
		try {
			$obj = static::get($args);
			return $obj;
		}
		catch (Exception $e) {
			return null;
		}
	}
	
	public static function delete_or_ignore($args) {
		try {
			$obj = static::get($args);
			$obj->delete();
		}
		catch (Exception $e) {
		}
	}
	
	// Add a new field
	protected function add_field($name, $type) {
		if ($type->is_pk_field()) {
			$new_fields = array();
			$new_fields[$name] = $type;
			foreach ($this->fields as $name => $field)
				if (!$field->is_pk_field())
					$new_fields[$name] = $field;
			$this->fields = $new_fields;
		} else {
			$this->fields[$name] = $type;
		}
		$type->setup($this, $name);
	}
	
	// Add a new safe field
	protected function add_safe_field($name, $type) {
		$this->add_field($name, $type);
		$this->safe_fields[] = $name;
	}
	
	public function setValid($val) {
		$this->_valid_model = $val;
	}
	
	public function get_table_name() {
		return strtolower(get_class($this));
	}
	
	// Get fields
	public function get_fields() {
		return $this->fields;
	}
	
	// Get field
	public function get_field($name) {
		return $this->__get("_" . $name);
	}
	
	public function __get($name) {
		$is_safe = in_array($name, $this->safe_fields);
		if ($name == "pk")
			$name = $this->_pk();
		if ($name == "_pk")
			$name = "_" . $this->_pk();
		if (isset($this->fields[$name])) {
			$val = $this->fields[$name]->get_value();
			return ($is_safe || !is_string($val)) ? $val : strip_tags($val);
		}
		if (starts_with($name, "_")) {
			$base_name = substr($name, 1);
			if (isset($this->fields[$base_name]))
				return $this->fields[$base_name];
		}
		throw new FieldException($GLOBALS["i18n"]["fieldne"] . " '$name'.");
	}
	
	public function __set($name, $value) {
		if ($name == "pk")
			$name = $this->_pk();
		if (isset($this->fields[$name]))
			$this->fields[$name]->set_value($value);
		else
			throw new FieldException($GLOBALS["i18n"]["fieldne"] . " '$name'.");
	}
	
	// Basically: Is $name a valid field name? (Doesnt say if the field has been set)
	public function __isset($name) {
		if ($name == "pk")
			return True;
		return isset($this->fields[$name]) && $this->fields[$name]->is_set();
	}
	
	// Unsetting a field resets it to default value
	public function __unset($name) {
		if ($name == "pk")
			return;
		if ($this->__isset($name))
			$this->fields[$name]->reset();
	}
	
	// Returns the query to create the table in the database
	public function db_create_query($db) {
		$table_name = $this->get_table_name();
		$post_scripts = "";
		$SQL = "CREATE TABLE \"" . $table_name . "\" (";
		$i = 0;
		foreach ($this->get_fields() as $name => $field) {
			if ($i > 0) $SQL .= ", ";
			$SQL .= $field->db_create_query($db, $name, $table_name);
			$i++;
			$post_query = $field->db_post_create_query($db, $name, $table_name);
			if (strlen($post_scripts) > 0 && strlen($post_query) > 0)
				$post_scripts .= ", ";
			if (strlen($post_query) > 0)
				$post_scripts .= $post_query;
		}
		if (strlen($post_scripts) > 0)
			$SQL .= ", " . $post_scripts;
		$SQL .= ");";
		
		return $SQL;
	}
	
	public function db_create_extra_queries_pre($db, $table_name) {
		$extra_scripts = array();
		foreach ($this->get_fields() as $name => $field) {
			$query = $field->db_extra_create_query_pre($db, $name, $table_name);
			if (strlen($query) > 0)
				array_push($extra_scripts, $query);
		}
		return $extra_scripts;
	}
	
	public function db_create_extra_queries_post($db, $table_name) {
		$extra_scripts = array();
		foreach ($this->get_fields() as $name => $field) {
			$query = $field->db_extra_create_query_post($db, $name, $table_name);
			if (strlen($query) > 0)
				array_push($extra_scripts, $query);
		}
		return $extra_scripts;
	}
	
	protected function table_exists() {
		$db = Database::create($this->_using);
		if ($db)
			return in_array($this->get_table_name(), $db->get_tables());
	}
	
	// Creates the table in the database if needed
	public function create_table() {
		if (!$this->table_exists()) {
			$db = Database::create($this->_using);
			if (!$db)
				return false;
			$table_name = $this->get_table_name();
			foreach($this->db_create_extra_queries_pre($db, $table_name) as $query)
				$db->query($query);
			$res = $db->query($this->db_create_query($db));
			foreach($this->db_create_extra_queries_post($db, $table_name) as $query)
				$db->query($query);
			ContentType::of($this->get_content_type());
			return $res;
		}
		return true;
	}
	
	// Verifies that the table structure in the database is up-to-date
	// NOTE: Currently only detects field name changes, not type changes
	public function verify_table() {
		$this->create_table();
		$db = Database::create($this->_using);
		if (!$db)
			return false;
		$table_name = $this->get_table_name();
		$fields = $this->get_fields();
		$columns = $db->get_columns($table_name);
		foreach ($columns as $column => $type) {
			if (!array_key_exists($column, $fields))
				throw new TableValidationException($column . " ".$GLOBALS["i18n"]["nolongerpart"]." " . $table_name);
		}
		foreach ($fields as $field => $type) {
			if (!array_key_exists($field, $columns))
				throw new TableValidationException($field . " ".$GLOBALS["i18n"]["shdin"]." " . $table_name);
		}
		return True;
	}
	
	// Validates the model
	public function validate() {
		$this->errors = array();
		foreach ($this->get_fields() as $field_name => $field) {
			if (!$field->validate()) {
				$this->errors = array_merge($this->errors, $field->errors);
				return False;
			}
		}
		return True;
	}

	// Provides validation errors
	public function get_errors() {
		return $this->errors;
	}
	
	public function get_error_string() {
		$str = "";
		foreach ($this->get_errors() as $error) {
			if (strlen($str) > 0)
				$str .= "\n";
			$str .= $error;
		}
		return $str;
	}
	
	// Insert the object to the database
	public function insert_query($db) {
		$keys = "";
		$values = "";
		foreach ($this->get_fields() as $field_name => $field) {
			if ($field->hide_from_query)
				continue;
			$is_safe = in_array($field_name, $this->safe_fields);
			if (strlen($keys) > 0) {
				$keys .= ", ";
				$values .= ", ";
			}
			$keys .= "\"" . $field_name . "\"";
			$val = $field->sql_value($db);
			$val = $is_safe ? $val : strip_tags($val);
			if (strlen($val) <= 0)
				$val = "''";
			$values .= $val;
		}
		$extra = "";
		if ($db->get_type() == "psql")
			$extra = " RETURNING \"" . $this->_pk() . "\"";
		return "INSERT INTO \"" . $this->get_table_name() . "\" (" . $keys . ") VALUES (" . $values . ")" . $extra . ";";
	}
	
	// Update the object in the database
	public function update_query($db) {
		$old_object = static::get($this->pk);
		$query = "UPDATE \"" . $this->get_table_name() . "\" SET ";
		$go = False;
		foreach ($old_object->get_fields() as $name => $field) {
			if ($field->hide_from_query)
				continue;
			$is_safe = in_array($name, $this->safe_fields);
			$new_val = $this->fields[$name];
			if (strval($field->sql_value($db)) !== strval($new_val->sql_value($db))) {
				if ($go)
					$query .= ", ";
				$val = $new_val->sql_value($db);
				$val = $is_safe ? $val : strip_tags($val);
				$query .= $name . "=" . $val;
				$go = True;
			}
		}
		$query .= " WHERE " . $this->_pk() . "=" . $db->escape_string($this->pk);
		if ($go)
			return $query;
		return ""; // Nothing to do
	}
	
	public function pre_save() { return true; }
	public function post_save($pk) { }
	public function pre_create() {}
	public function post_create() {}
	public function pre_update() {}
	public function post_update() {}
	
	// Saves the object to the database, returns ID
	public function save() {
		if (!$this->pre_save())
			throw new ValidationException($GLOBALS["i18n"]["saveerror1"]);
		if (!$this->_valid_model)
			throw new ValidationException($GLOBALS["i18n"]["saveerror2"] . " " . get_class($this));
		if (!$this->validate())
			throw new ValidationException($GLOBALS["i18n"]["error1"] . get_class($this) . $GLOBALS["i18n"]["saveerror3"] . "<br />" . $this->get_error_string());
		$this->create_table();
		$db = Database::create($this->_using);
		if (!$db)
			return false;
		$query = "";
		
		foreach ($this->get_fields() as $name => $field)
			$field->pre_save($this, $this->from_db);
			
		if (!$this->from_db) {
			$this->pre_create();
			$query = $db->query($this->insert_query($db));
			$id = 0;
			if ($db->get_type() == "psql") {
				$row = $db->fetch($query);
				$id = $row[0];
			}
			if ($db->get_type() == "mysql")
				$id = mysql_insert_id();
			$this->pk = intval($id);
			$this->from_db = true;
			$this->post_create();
		}
		else {
			$this->pre_update();
			$query = $this->update_query($db);
			if (strlen($query) > 0)
				$db->query($query);
			$this->post_update();
		}
		$this->post_save($this->pk);
		return $this->pk;
	}

	public function delete_query($db) {
		return "DELETE FROM \"" . $this->get_table_name() . "\" WHERE \"". $this->_pk() ."\"='" . $this->pk . "';";
	}

	/* Returns True on success, False on failure */
	public function delete() {
		if (!$this->from_db)
			return False;
		$db = Database::create($this->_using);
		if (!$db)
			return false;
		$db->query($this->delete_query($db));
		return True;
	}
}

?>

