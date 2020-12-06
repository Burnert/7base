<?php

session_start();

require_once('./php/localisation.php');

require_once('./php/database/dbmanager.php');
require_once("./php/database/dbinfo.php");

DatabaseManager::create();
DatabaseManager::get()->connect(db\HOST, db\LOGIN, db\PASSWORD);
DatabaseManager::get()->select_database(db\DATABASE);

$request = $_REQUEST["request"];
$args = explode(" ", $request);

// Settings
if ($args[0] == "apply_settings") {
  $_SESSION["language"] = $_REQUEST["lang"];
  echo "Changed language to <b>lang_" . $_SESSION["language"] . "</b>";
  $_SESSION["theme"] = $_REQUEST["theme"];
  echo "Changed theme to <b>theme_" . $_SESSION["theme"] . "</b>";
}
// Database
else if ($args[0] == "add_entries") {
  if (!isset($_REQUEST["name"]) ||
      !isset($_REQUEST["columns"]) ||
      !isset($_REQUEST["entries"])) {
    echo "Bad request";
  }
  $table_name = $_REQUEST["name"];
  $columns_json = $_REQUEST["columns"];
  $columns = json_decode($columns_json);
  $entries_json = $_REQUEST["entries"];
  $entries = json_decode($entries_json);
  DatabaseManager::get()->add_entries($table_name, $columns, $entries);
}
else if ($args[0] == "delete_entries_unique") {
  if (!isset($_REQUEST["name"]) ||
      !isset($_REQUEST["entries"])) {
    echo "Bad request";
  }
  $table_name = $_REQUEST["name"];
  $unique_key = $_REQUEST["key"];
  $entries_json = $_REQUEST["entries"];
  $entries = json_decode($entries_json);
  DatabaseManager::get()->delete_entries_unique($table_name, $unique_key, $entries);
}
else if ($args[0] == "describe_table") {
  DatabaseManager::get()->describe_table();
}
// Localisation
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
