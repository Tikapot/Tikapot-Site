<?php
/*
 * Tikapot Auth App
 *
 */

global $signal_manager;

require_once(home_dir . "contrib/auth/models.php");
require_once(home_dir . "framework/forms.php");
require_once(home_dir . "framework/form_fields/init.php");

function auth_init($request) {
	if (isset($request->get['verify_email'])) {
		$confirmationcodes = ConfirmationCode::find(array("code" => $request->get['code']));
		if ($confirmationcodes->count() > 0) {
			$code = $confirmationcodes->get(0);
			if ($code->user->email == $request->get['verify_email']) {
				$code->user->status = User::$_STATUS['live'];
				$code->user->save();
				$code->delete();
				$request->email_verification = true;
			}
		}
		if (!isset($request->email_verification))
			$request->email_verification = false;
	}
	
	$request->user = new User();
	if (isset($_SESSION['user']) && UserSession::check_session($_SESSION['user']['userid'], $_SESSION['user']['keycode'])) {
		try {
			$request->user = User::get($_SESSION['user']['userid']);
		}
		catch(Exception $e) {
			User::logout();
		}
	}
	
	if (!$request->user->logged_in()) {
		/* Add login form */
		$request->login_form = new Form(array(
			"general" => array(
				"username" => new CharFormField("Username"),
				"password" => new PasswordField("Password"),
			)
		), $request->fullPath, "POST");
		
		/* Add registration form */
		$request->register_form = new Form(array(
			"general" => array(
				"email" => new EmailFormField("Email", "", array("placeholder"=>"Your School Email...")),
				"password" => new PasswordField("Password", "", array("placeholder"=>"Password...")),
				"password2" => new PasswordField("Password (Again)", "", array("placeholder"=>"Password... (Again)")),
			)
		), $request->fullPath, "POST");
		
		if (isset($_POST['control_formid'])) {
			/* Handle login form */
			try {
				$request->login_form->load_post_data($_POST);
				if (!$request->login_form->get_value("general", "username") || !$request->login_form->get_value("general", "password"))
					return;
				User::login($request, $request->login_form->get_value("general", "username"), $request->login_form->get_value("general", "password"));
				$request->login_form->clear_data();
				return;
			} catch(Exception $e) {	}
			
			/* Handle registration form */
			try {
				$request->register_form->load_post_data($_POST);
				if (!$request->register_form->get_value("general", "password") || !$request->register_form->get_value("general", "password2"))
					return;
				if (!$request->register_form->get_value("general", "password") == $request->register_form->get_value("general", "password2")) {
					$request->message("Passwords didnt match!");
					return;
				}
				try {
					User::create_user($request->register_form->get_value("general", "email"), $request->register_form->get_value("general", "password"), $request->register_form->get_value("general", "email"), User::$_TYPE['student']);
					$request->register_form->clear_data();
					User::login($request->register_form->get_value("general", "email"), $request->register_form->get_value("general", "password"));
				} catch(Exception $e) { $request->message($e->getMessage()); }
			} catch(Exception $e) {	}
		}
	}
}

$signal_manager->hook("page_load_start", "auth_init");
?>

