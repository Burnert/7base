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

require_once('./php/login.php');
session_log_in();

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
  <?php if ($logged_in && $selected_database): ?>

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
  if (isset($_GET["action"]) && !isset($_GET["table"])) {
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
    echo "WORK IN PROGRESS!";
  }
  // View table
  else if (isset($_GET["table"])) {
    $tablename = $_GET["table"];
    $columns = DatabaseManager::get()->describe_table($tablename);
    $condition = null;
    if (isset($_GET["search_column"]) && isset($_GET["search_query"])) {
      $search_column = $_GET["search_column"];
      $search_query = $_GET["search_query"];
      $condition = "`$search_column` LIKE '%$search_query%'";
    }
    $rows = DatabaseManager::get()->select_from_table($tablename, null, $condition);

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

  <?php elseif ($logged_in): ?>

  <section class="splash">
    <div class="databases-window">
      <header>
        <img src="./gfx/7base.png" alt="7Base" class="logo">
      </header>
      <div class="content">
        <h4><?php loc("select_database") ?></h4>
        <div>
          <ul>
          <?php
          $databases = DatabaseManager::get()->get_all_databases();
          foreach ($databases as $database) {
          ?>
            <li>
              <button class="select_db" value="<?php echo $database ?>"><?php echo $database ?></button>
            </li>
          <?php
          }
          ?>
          </ul>
        </div>
        <script>
          document.querySelectorAll('button.select_db').forEach(button => button.addEventListener('click', (event) => {
            sendInterfaceRequest('select_database', { database: event.target.value }).then(location.reload());
          }));
        </script>
        <div class="horiz">
          <button class="b-logout"><?php loc("log_out") ?></button>
        </div>
      </div>
    </div>
  </section>

  <?php else: ?>

  <section class="splash">
    <div class="login-window">
      <header>
        <img src="./gfx/7base.png" alt="7Base" class="logo">
      </header>
      <div class="content">
        <form action="index.php" method="POST" autocomplete="off" id="login-form">
          <div class="horiz sb">
            <label for="login"><span><?php loc("user_login") ?></span></label>
            <input type="text" name="user_login" id="login" required>
          </div>
          <div class="horiz sb">
            <label for="password"><span><?php loc("user_password") ?></span></label>
            <input type="password" name="user_password" id="password">
          </div>
        </form>
        <div class="horiz">
          <button type="submit" form="login-form"><?php loc("log_in") ?></button>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>
</body>
</html>

<?php

$dbmanager = DatabaseManager::get();
if ($dbmanager) {
  $dbmanager->close();
}

?>