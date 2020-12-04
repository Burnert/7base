<?php

session_start();

$request = $_REQUEST["request"];
$args = explode(":", $request);

if ($args[0] == "apply_settings") {
	$_SESSION["language"] = $_REQUEST["lang"];
}

echo "Changed language to <b>lang_" . $_SESSION["language"] . "</b>";

?>