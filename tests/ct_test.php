<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "contrib/auth/models.php");
require_once(home_dir . "framework/model_query.php");
require_once(home_dir . "framework/database.php");
require_once(home_dir . "framework/models.php");

class CTTestM extends Model {}

class CTTest extends UnitTestCase {
	function testCT() {
		$obj = new ContentType();
		$obj->create_table();
		$db = Database::create();
		$db->query("DELETE FROM " . $obj->get_table_name() . ";");
		if ($db->get_type() == "psql")
			$db->query("ALTER SEQUENCE " . $obj->get_table_name() . "_id_seq RESTART WITH 1;");
		$this->assertEqual(ContentType::of($obj), 1);
		$this->assertEqual(ContentType::of($obj), 1);
		$obj2 = new CTTestM();
		$this->assertEqual(ContentType::of($obj2), 2);
		
		$ct = ContentType::get(ContentType::of($obj2));
		$object = $ct->obtain();
		$this->assertTrue($object);
	}
}

?>

