<?php

session_start();

require_once('./php/localisation.php');

require_once('./php/database/dbmanager.php');
require_once("./php/database/dbinfo.php");

require_once('./php/login.php');

$request = $_REQUEST["request"];
$args = explode(" ", $request);

// Session
if ($args[0] == "destroy_session") {
  session_destroy();
  echo "Session has been destroyed";
}
else if ($args[0] == "unset_selected_db") {
  unset($_SESSION["database"]);
  echo "Database has been unset";
}
// Settings
else if ($args[0] == "apply_settings") {
  $_SESSION["language"] = $_REQUEST["lang"];
  echo "Changed language to <b>lang_" . $_SESSION["language"] . "</b>";
  $_SESSION["theme"] = $_REQUEST["theme"];
  echo "Changed theme to <b>theme_" . $_SESSION["theme"] . "</b>";
}
// Database
else if ($args[0] == "select_database") {
  session_log_in();
  if (!isset($_REQUEST["database"])) {
    echo "Bad request";
    die;
  }
  $_SESSION["database"] = $_REQUEST["database"];
  $selected_database = $_SESSION["database"];
  DatabaseManager::get()->select_database($selected_database);
}
else if ($args[0] == "add_entries") {
  session_log_in();
  if (!isset($_REQUEST["name"]) ||
      !isset($_REQUEST["columns"]) ||
      !isset($_REQUEST["entries"])) {
    echo "Bad request";
    die;
  }
  $table_name = $_REQUEST["name"];
  $columns_json = $_REQUEST["columns"];
  $columns = json_decode($columns_json);
  $entries_json = $_REQUEST["entries"];
  $entries = json_decode($entries_json);
  DatabaseManager::get()->add_entries($table_name, $columns, $entries);
}
else if ($args[0] == "delete_entries_unique") {
  session_log_in();
  if (!isset($_REQUEST["name"]) ||
      !isset($_REQUEST["key"]) ||
      !isset($_REQUEST["entries"])) {
    echo "Bad request";
    die;
  }
  $table_name = $_REQUEST["name"];
  $unique_key = $_REQUEST["key"];
  $entries_json = $_REQUEST["entries"];
  $entries = json_decode($entries_json);
  DatabaseManager::get()->delete_entries_unique($table_name, $unique_key, $entries);
}
else if ($args[0] == "update_entries_unique") {
  session_log_in();
  if (!isset($_REQUEST["name"]) ||
      !isset($_REQUEST["key"]) ||
      !isset($_REQUEST["entriesOld"]) ||
      !isset($_REQUEST["entriesNew"])) {
    echo "Bad request";
    die;
  }
  $table_name = $_REQUEST["name"];
  $unique_key = $_REQUEST["key"];
  $entries_old_json = $_REQUEST["entriesOld"];
  $entries_old = json_decode($entries_old_json);
  $entries_new_json = $_REQUEST["entriesNew"];
  $entries_new = json_decode($entries_new_json);
  $entries = [];
  $entries["old"] = $entries_old;
  $entries["new"] = $entries_new;
  DatabaseManager::get()->update_entries_unique($table_name, $unique_key, $entries);
}
else if ($args[0] == "describe_table") {
  session_log_in();
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

if ($logged_in) {
  DatabaseManager::get()->close();
}

?>
