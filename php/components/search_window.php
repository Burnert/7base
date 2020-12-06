<?php

function search_window($table_name, $columns) {
  global $search_column, $search_query;
?>
<div class="search-window" style="display: none;">
  <div class="top-bar horiz sb v-center">
    <h4><?php loc("search_in_table") ?></h4>
    <button class="exit soft nobg">
      <i class="material-icons">clear</i>
    </button>
  </div>
  <div class="content">
    <h4><?php loc("search_in"); echo " " . $table_name ?></h4>
    <form id="search-form" action="index.php" method="GET" autocomplete="off">
      <input type="hidden" name="table" value="<?php echo $table_name ?>">
      <div class="horiz sb">
        <label for="column-name"><span><?php loc("search_column_name") ?></span></label>
        <select name="search_column" id="column-name">
          <?php
          foreach ($columns as $column) {
          ?>
          <option value="<?php echo $column["Field"] ?>" <?php if ($column["Field"] == $search_column) echo "selected" ?>><?php echo $column["Field"] ?></option>
          <?php
          }
          ?>
        </select>
      </div>
      <div class="horiz sb">
        <label for="search-query"><span><?php loc("search_query") ?></span></label>
        <input type="text" name="search_query" id="search-query" <?php if ($search_query && $search_query != "") echo "value='$search_query'" ?>>
      </div>
    </form>
    <div class="horiz">
      <button type="submit" class="confirm-search" form="search-form"><?php loc("search") ?></button>
    </div>
  </div>
</div>
<?php
}

?>