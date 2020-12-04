<?php

$themes = [];
function add_theme($theme_id, $theme_name) {
  global $themes;

  $theme = [];
  $theme["id"] = $theme_id;
  $theme["name"] = $theme_name;
  array_push($themes, $theme);
}

add_theme("null", "theme_none");
add_theme("theme-1", "theme_theme-1");

?>