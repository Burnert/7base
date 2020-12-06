<?php

$themes = [];

if (!isset($_SESSION["theme"])) {
  $_SESSION["theme"] = "null";
}
$current_theme = $_SESSION["theme"];

function add_theme($theme_id, $theme_name) {
  global $themes;

  $theme = [];
  $theme["id"] = $theme_id;
  $theme["name"] = $theme_name;
  array_push($themes, $theme);
}

add_theme("null", "theme_none");
add_theme("theme-1", "theme_theme-1");
add_theme("theme-2", "theme_theme-2");

?>