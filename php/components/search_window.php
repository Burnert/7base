<?php

function search_window($columns) {
?>
<div class="search-window" style="display: none;">
  <div class="top-bar horiz sb v-center">
    <h4><?php loc("search_in_table") ?></h4>
    <button class="exit soft nobg">
      <i class="material-icons">clear</i>
    </button>
  </div>
  <div class="content">
    <h4><?php loc("search_options") ?></h4>
    <div>
      <div class="horiz sb">
        <label for="column-name"><span><?php loc("column_name") ?></span></label>
        <select name="column-name" id="column-name">
          <?php
          foreach ($columns as $column) {
          ?>
          <option value="<?php echo $column["Field"] ?>"><?php echo $column["Field"] ?></option>
          <?php
          }
          ?>
        </select>
      </div>
      <div class="horiz sb">
        <label for="search-query"><span><?php loc("search_query") ?></span></label>
        <input type="text" name="search-query" id="search-query">
      </div>
    </div>
    <div class="horiz">
      <button class="confirm-search"><?php loc("search") ?></button>
    </div>
  </div>
</div>
<?php
}

?>