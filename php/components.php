<?php

// Menu button

function menu_button($menu_id) {
?>
  <div class="menu-button" menu="<?php echo $menu_id ?>">
    <span class="menu-bar" style="top: 0px;"></span>
    <span class="menu-bar" style="top: 15px;"></span>
    <span class="menu-bar" style="top: 30px;"></span>
  </div>
<?php
}

// Menu

function menu($menu_id) {
  global $dbmanager, $langdir, $lang, $language;

  $menu_links = $dbmanager->get_all_tables();
?>
  <div class="menu" id="<?php echo $menu_id ?>">
    <h4><?php loc("table_manager") ?></h4>
    <div class="table-manager">
      <ul>
<?php
  foreach ($menu_links as $value) {
?>
        <li>
          <a href="index.php?table=<?php echo $value ?>"><?php echo $value ?></a>
          <div class="overlay table-controls horiz-sb">
            <a href="index.php?table=<?php echo $value ?>&action=edit" class="b-edit-table" title="<?php loc("edit_table") ?>">
              <i class="material-icons">create</i>
            </a>
            <a href="index.php?table=<?php echo $value ?>&action=delete" class="b-delete-table" title="<?php loc("delete_table") ?>">
              <i class="material-icons">delete_forever</i>
            </a>
          </div>
        </li>
<?php
  }
?>
      </ul>
      <div class="horiz">
        <a href="index.php?action=create" id="b-create-table" class="button"><?php loc("add_table") ?></a>
      </div>
    </div>
    <div class="spacer-v"></div>
    <h4><?php loc("settings") ?></h4>
    <div class="settings">
      <ul>
        <li>
          <label for="language"><?php loc("language") ?></label>
          <select name="language" id="language">
<?php
          foreach ($langdir as $l) {
            $langname = $lang["lang_" . $l];
?>
            <option value='<?php echo $l ?>' <?php if ($l == $language) echo "selected" ?> ><?php echo $langname ?></option>
<?php
          }
?>
          </select>
        </li>
      </ul>
      <div class="horiz">
        <button class="b-refresh"><?php loc("settings_apply") ?></button>
      </div>
    </div>
  </div>
<?php
}

// Table view

function table_view($rows, $columns = null) {
?>
  <div class="table-wrapper">
  <table class="table-view">
  <?php if ($columns):
    echo "<tr>";
    foreach ($columns as $column) {
?>
      <th><?php echo $column["Field"] ?></th>
<?php
    }
    echo "</tr>";
  endif;

  if ($rows):
    foreach ($rows as $row) {
      echo "<tr>";
      foreach ($row as $value) {
        echo "<td>";
        if ($value) {
          echo $value;
        }
        else {
          echo "<i>NULL</i>";
        }
        echo "</td>";
      } 
      echo "</tr>";
    }
  endif;
?>
  </table>
  </div>
<?php
}

// Table creation section

function table_create() {
?>
  <div class="create-container">
    <form action="./php/database/create_table.php" method="POST" autocomplete="off">
      <div class="vert block-center fit">
        <div class="input-generic">
          <label for="cr-name"><?php loc("table_name") ?></label>
          <input type="text" name="name" id="cr-name">
        </div>
        <div class="spacer-v"></div>
        <h3><?php loc("columns") ?></h3>
      </div>
      <div class="table-wrapper">
        <table class="create-table">
          <tbody>
            <tr>
              <th><?php loc("name") ?></th>
              <th><?php loc("type") ?></th>
              <th><?php loc("length"); echo "/"; loc("values") ?></th>
              <th><?php loc("default_value") ?></th>
              <th><?php loc("nullable") ?></th>
              <th><?php loc("index") ?></th>
              <th><?php loc("auto_increment") ?></th>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="7">
                <div>
                  <button type="button" class="soft" id="b-add-column">
                    <i class="material-icons">add</i>
                  </button>
                </div>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </form>
  </div>
<?php
}












?>
