<?php
/*
 * Tikapot Urls
 * 
 * Perhaps error views could be added here too
 * but for now that is not the responsibility
 * of this project
 */

require_once(home_dir . "framework/views/init.php");

new i18nJSView("/tikapot/i18n.js");
new CaptchaView("/tikapot/api/captcha/");
new CaptchaVerificationView("/tikapot/api/captcha/verify/");
?>
