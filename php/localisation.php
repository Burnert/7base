<?php

include_once("./lang/lang.php");

if (!isset($_SESSION["language"])) {
  $_SESSION["language"] = "en";
}
$language = $_SESSION["language"];

include_once("./lang/en.php");
$langdir = array_map(function($value) {
  return substr($value, 0, 2);
}, array_filter(scandir("./lang"), function($value) {
  return strpos($value, '.php') && strlen($value) == 6;
}));

if (file_exists("./lang/$language.php")) {
  include_once("./lang/$language.php");
}

function loc($key) {
  global $lang;
  if (array_key_exists($key, $lang)) {
    echo $lang[$key];
  }
  else {
    echo $key;
  }
}

?>