<?php
/*
 * Tikapot Signal Manager
 *
 */

class SignalException extends Exception {}

class SignalManager
{
	private $signals;
	
	public function __construct() {
		$this->signals = array();
	}
	
	public function register() {
		$arg_list = func_get_args();
   		foreach ($arg_list as $signal) {
			if (isset($this->signals[$signal]))
				throw new SignalException($GLOBALS["i18n"]["sigerr1"] . " " . $signal);
			$this->signals[$signal] = array();
		}
	}
	
	public function hook($signal, $function, $obj = Null) {
		if (!isset($this->signals[$signal]))
			throw new SignalException($GLOBALS["i18n"]["sigerr2"] . " " . $signal);
		$this->signals[$signal][$function] = $obj;
	}
	
	public function fire($signal, $obj = Null) {
		if (!isset($this->signals[$signal]))
			throw new SignalException($GLOBALS["i18n"]["sigerr2"] . " " . $signal);
		foreach ($this->signals[$signal] as $function => $object) {
			if ($object)
				if(method_exists($object, $function))
					call_user_func_array(array($object, $function), array($obj));
				else
					throw new SignalException($GLOBALS["i18n"]["sigerr3"] . " " . $function);
			else
				$function($obj);
		}
	}
}

?>

