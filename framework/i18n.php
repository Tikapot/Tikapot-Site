<?php
/*
 * Tikapot internationalisation
 *
 */

class i18n implements Iterator, Countable, arrayaccess
{
	private $map, $position;
	
	public function __construct($map) {
		$this->map = $map;
		$this->position = 0;
	}

	public function buildJS() {
		$js = "var i18n = new Object();\n";
		foreach ($this->map as $name => $val) {
			$name = str_replace(" ", "_", $name);
			if (!preg_match("/^[a-z]/", $name)) {
				$name = "i" . $name;
			}
			$val = str_replace("\"", "\\\"", $val);
			$js .= "i18n." . $name . " = \"".$val."\";\n";
		}
		return $js;
	}
	
	public function count() {
		return count($map);
	}
	
	/* Iterator */
	public function rewind() {
		$this->position = 0;
	}
	
	public function current() {
		return $this->map[$this->position];
	}
	
	public function key() {
		return $this->position;
	}
	
	public function next() {
		++$this->position;
	}
	
	public function valid() {
		return isset($this->map[$this->position]);
	}
	/* End Iterator */
	
	/* Array Access */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->map[] = $value;
		} else {
			$this->map[$offset] = $value;
		}
	}
	
	public function offsetExists($offset) {
		return isset($this->map[$offset]);
	}
	
	public function offsetUnset($offset) {
		unset($this->map[$offset]);
	}
	
	public function offsetGet($offset) {
		if (isset($this->map[$offset]))
			return $this->map[$offset];
		return debug ? "#mtrns#" : "";
	}
	/* End Array Access */
}

?>

