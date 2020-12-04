<?php
session_start();

require_once("./php/localisation.php");

require_once("./php/database/dbinfo.php");
require_once("./php/database/dbmanager.php");

$dbmanager = new DatabaseManager();
$dbmanager->connect(db\HOST, db\LOGIN, db\PASSWORD);
$dbmanager->select_database(db\DATABASE);

require_once("./php/components.php");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta type="keywords" content="baza">
  <link rel="stylesheet" href="./css/normalize.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <title><?php loc("management_system") ?></title>
  <script src="./js/main.js"></script>
  <script src="./js/components.js"></script>
</head>
<body>
  <header>
    <h1>7Base</h1>
    <p><?php loc("management_system") ?></p>
    <div class="overlay">
      <img src="./gfx/7base.png" alt="7Base">
      <?php menu_button("mainmenu"); ?>
      <script>
        if (mainMenuActive) {
          const mainMenuButton = document.querySelector('*[menu=mainmenu]');
          mainMenuButton.classList.add('active');
        }
      </script>
    </div>
  </header>
  <?php menu("mainmenu"); ?>
  <script>
    if (mainMenuActive) {
      const mainMenu = document.getElementById('mainmenu');
      mainMenu.classList.add('active');
    }
  </script>
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
    $columns = $dbmanager->describe_table($tablename);
    $rows = $dbmanager->select_from_table($tablename);

    table_view($rows, $columns);
    if (!$rows) {
?>
      <p class="notice"><?php loc("table_empty") ?></p>
<?php
    }
  }
  ?>
  </section>
  <footer>
    <p>Jacek Rogal &copy;</p>
  </footer>
  <script>
  document.querySelectorAll('.menu').forEach(menu => setTimeout(() => menu.style.display = 'block', 50));
  </script>
</body>
</html>
