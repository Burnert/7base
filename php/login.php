<?php

if (isset($_POST["user_login"])) {
  $_SESSION["user_login"] = $_POST["user_login"];
  $_SESSION["user_password"] = $_POST["user_password"];
  unset($_POST["user_login"]);
  unset($_POST["user_password"]);
}

$logged_in = false;
$selected_database = null;

function session_log_in() {
  global $logged_in, $selected_database;

  DatabaseManager::create();

  $logged_in = false;

  if (isset($_SESSION["user_login"]) && isset($_SESSION["user_password"])) {
    // echo $_SESSION["user_login"];
    DatabaseManager::get()->connect(db\HOST, db\LOGIN, db\PASSWORD);
    $users = DatabaseManager::get()->get_user($_SESSION["user_login"], $_SESSION["user_password"]);
    if (!empty($users)) {
      $logged_in = true;
    }
  }

  $selected_database = null;
  if (isset($_SESSION["database"])) {
    $selected_database = $_SESSION["database"];
  }

  if ($logged_in) {
    $login = $_SESSION["user_login"];
    $password = $_SESSION["user_password"];
  
    DatabaseManager::create();
    DatabaseManager::get()->connect(db\HOST, $login, $password);
  
    if ($selected_database) {
      DatabaseManager::get()->select_database($selected_database);
    }
  }
}

?>
