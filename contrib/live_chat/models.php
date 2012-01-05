<?php
/*
 * Tikapot Live Chat App
 *
 */

require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");

/*
 * A Chat User
 */
class ChatUser extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("session_key", new CharField($max_length=250));
		$this->add_field("name", new CharField($max_length=250));
		$this->add_field("is_staff", new BooleanField());
	}
}

/*
 * A Chat Message
 */
class ChatMessage extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("user", new FKField("live_chat.ChatUser"));
		$this->add_field("message", new TextField());
	}
}

/*
 * A Chat Session stores the 2 users chatting, as well as a message log
 */
class ChatSession extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("type", new CharField($max_length=120)); // Would be "department" on business sites
		$this->add_field("users", new M2MField("live_chat.ChatUser"));
		$this->add_field("messages", new M2MField("live_chat.ChatMessage"));
	}
}

?>

