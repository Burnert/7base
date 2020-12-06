<?php

function var_pre($var) {
  echo '<pre>';
  var_export($var);
  echo '</pre>';
}

session_start();

require_once("./php/localisation.php");
require_once("./php/themes.php");

// Database
require_once("./php/database/dbinfo.php");
require_once("./php/database/dbmanager.php");

DatabaseManager::create();
DatabaseManager::get()->connect(db\HOST, db\LOGIN, db\PASSWORD);
DatabaseManager::get()->select_database(db\DATABASE);

// Components
require_once("./php/components/menu.php");
require_once("./php/components/table_create.php");
require_once("./php/components/table_view.php");
?>
<!DOCTYPE html>
<html lang="<?php echo $language ?>" <?php echo $current_theme == "null" ? "" : "class='$current_theme'" ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta type="keywords" content="baza">
  <!-- Styles -->
  <link rel="stylesheet" href="./css/normalize.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/components/menu.css">
  <link rel="stylesheet" href="./css/components/table_view.css">
  <link rel="stylesheet" href="./css/components/search_window.css">
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  
  <title><?php loc("management_system") ?></title>
  <script src="./js/main.js"></script>
  <!-- Components -->
  <script src="./js/components/shared.js"></script>
  <script src="./js/components/dialogue_box.js"></script>
  <script src="./js/components/menu.js"></script>
  <script src="./js/components/create_table.js"></script>
  <script src="./js/components/table_view.js"></script>
  <script src="./js/components/search_window.js"></script>
</head>
<body>
  <header class="main">
    <h1>7Base</h1>
    <p><?php loc("management_system") ?></p>
    <div class="overlay">
      <img src="./gfx/7base.png" alt="7Base">
      <?php menu_button("mainmenu"); ?>
    </div>
  </header>
  <?php menu("mainmenu"); ?>
  <section class="main">
  <?php
  // Plain action
  if (isset($_GET["action"])) {
    $action = $_GET["action"];
    switch ($action) {
      case "create": {
        table_create();
      } break;
    }
  }
  // Action on a table
  else if (isset($_GET["action"]) && isset($_GET["table"])) {
    $action = $_GET["action"];
    switch ($action) {
      case "edit": {

      } break;
      case "delete": {

      } break;
    }
  }
  // View table
  else if (isset($_GET["table"])) {
    $tablename = $_GET["table"];
    $columns = DatabaseManager::get()->describe_table($tablename);
    $rows = DatabaseManager::get()->select_from_table($tablename);

    table_view($tablename, $rows, $columns);
  }
  ?>
  </section>
  <footer>
    <p>Jacek Rogal &ndash; Burnert 2020 &copy; &ndash; <?php loc("all_rights_reserved") ?></p>
  </footer>
  <script>
  document.querySelectorAll('.menu').forEach(menu => setTimeout(() => menu.style.display = 'block', 50));
  </script>
</body>
</html>
