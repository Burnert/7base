<?php

function menu($menu_id) {
  global $langdir, $lang, $language, $themes, $current_theme;

  $menu_links = DatabaseManager::get()->get_all_tables();
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
        <li>
          <label for="theme"><?php loc("theme") ?></label>
          <select name="theme" id="theme">
<?php
          foreach ($themes as $theme) {
?>
            <option value='<?php echo $theme["id"] ?>' <?php if ($theme["id"] == $current_theme) echo "selected" ?> ><?php loc($theme["name"]) ?></option>
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

?>