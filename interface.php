<?php

session_start();

require_once('./php/localisation.php');
require_once('./php/database/dbmanager.php');

$request = $_REQUEST["request"];
$args = explode(":", $request);

if ($args[0] == "apply_settings") {
	$_SESSION["language"] = $_REQUEST["lang"];
	echo "Changed language to <b>lang_" . $_SESSION["language"] . "</b>";
}
else if ($args[0] == "add_entries") {
	$entries_json = $_REQUEST["entries"];
	$entries = json_decode($entries_json);
	
	DatabaseManager::get()->add_entries();
	var_dump(DatabaseManager::get());
}
else if ($args[0] == "loc") {
	
	if (count($args) > 1 && $args[1] == "m") {

	}
	else {
		if (isset($_REQUEST["entry"])) {
			$entry = $_REQUEST["entry"];
			echo $lang[$entry];
		}
		else {
			echo "NULL";
		}
	}
}

?>
