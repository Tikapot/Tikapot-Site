<?php
/*
 * Tikapot Auth Models
 * 
 * This file contains models that are essential for
 * the correct operation of tikapot core modules
 *
 */

require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "framework/session.php");

class ConfirmationCode extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("user", new FKField("auth.User"));
		$this->add_field("code", new CharField($max_length=10));
	}
	
	public function __toString() {
		return $this->code;
	}
	
	public static function genCode($user) {
		$loc = rand(1, 25);
		$str = sha1($loc.microtime());
		$code = new ConfirmationCode();
		$code->user = $user->pk;
		$code->code = substr($str, $loc, 10);
		$code->save();
		return $code;
	}
}

class UserSession extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("user", new FKField("auth.User"));
		$this->add_field("keycode", new CharField($max_length=40));
		$this->add_field("expires", new DateTimeField());
	}
	
	public static function check_session($userid, $keycode) {
		$arr = UserSession::find(array("user"=>$userid));
		if (count($arr) <= 0)
			return false;
		$session = $arr->get(0);
		return $session->keycode == $keycode;
	}
}

class AuthException extends Exception {}

class User extends Model
{
	public static $_STATUS = array(
		"registered" => "0",
		"live" => "1",
		"suspended" => "2"
	);

	public function __construct() {
		parent::__construct();
		$this->add_field("username", new CharField($max_length=40));
		$this->add_field("password", new CharField($max_length=40));
		$this->add_field("email", new CharField($max_length=50));
		$this->add_field("status", new CharField($max_length=2));
		$this->add_field("created", new DateTimeField($auto_now_add = True));
		$this->add_field("last_login", new DateTimeField($auto_now_add = True, $auto_now = True));
	}
	
	public function logged_in() {
		return isset($_SESSION['user']) && ($_SESSION['user']['userid'] == $this->pk);
	}
	
	public static function logout($usersession = Null) {
		if ($usersession)
			$usersession->delete();
		Session::delete("user");
	}
	
	private function update_session($usersession) {
		global $tp_options;
		$expiry = time();
		if (isset($tp_options['session_timeout']))
			$expiry += $tp_options['session_timeout'];
		$usersession->expires = date(DateTimeField::$FORMAT, $expiry);
	}
	
	private function get_new_session_key() {
		return sha1($this->pk + (microtime() * rand(0, 198)));
	}
	
	private function construct_session($new_session=False) {
		list($usersession, $created) = UserSession::get_or_create(array("user"=>$this->pk));
		if ($created || $new_session) {
			$usersession->keycode = $this->get_new_session_key();
			$this->update_session($usersession);
		} else {
			if ($usersession->expires < date(DateTimeField::$FORMAT, time())) {
				$this->logout($usersession);
				throw new AuthException("Your session has timed out");
				return;
			} else {
				if ($usersession->keycode != $_SESSION['user']['keycode']) {
					$this->logout($usersession);
					throw new AuthException("Error: session key does not match!");
				} else {
					$this->update_session($usersession);
				}
			}
		}
		$_SESSION['user'] = array("userid"=>$this->pk, "keycode"=>$usersession->keycode);
		$usersession->save();
	}
	
	public static function encode($password) {
		$salted = $password;
		global $tp_options;
		if (isset($tp_options["password_salt"]))
			$salted = $tp_options["password_salt"] . $salted;
		return sha1($salted);
	}
	
	public static function auth_encoded($username, $password, $new_session=False) {
		$arr = User::find(array("username"=>$username, "password"=>$password));
		if (count($arr) <= 0) {
			throw new AuthException("Username/Password incorrect!");
		}
		$user = $arr->get(0);
		$user->construct_session($new_session);
		$user->save(); // Update last_login
		return $user;
	}
	
	public static function login($request, $username, $password) {
		$request->user = User::auth_encoded($username, User::encode($password), True);
		return $request->user->logged_in();
	}
	
	/* Shortcut */
	public static function create_user($username, $password, $email, $status = "0") {
		$password = User::encode($password);
		if(User::find(array("username"=>$username))->count() > 0)
			throw new AuthException("Error: Username exists!");
		$user = User::create(array("username"=>$username, "password" => $password, "email" => $email, "status" => $status));
		$code = ConfirmationCode::genCode($user);
		return array($user, $code);
	}
	
	/* Shortcut */
	public static function delete_user($username) {
		try {
			$user = User::get(array("username"=>$username));
			$user->delete();
		}
		catch (Exception $e) {
			return false;
		}
		return true;
	}
}

?>

