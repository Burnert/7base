<?php

function table_view($name, $rows, $columns) {
  // Get all unique keys
  $unique_keys = array_filter($columns, function($column) {
    return $column["Key"] == "UNI";
  });
  // Get primary key
  $primary_key = array_reduce(array_filter($columns, function($column) {
    return $column["Key"] == "PRI";
  }), function($carry, $item) {
    return $item["Field"];
  });
  $has_unique_keys = !empty($unique_keys) || $primary_key;
  $primary_key_label = "key_primary";
  $unique_key_label = "key_unique";
?>
  <script>
    const currentTable = {
      columns: JSON.parse('<?php echo json_encode($columns) ?>'),
      rows: JSON.parse('<?php echo json_encode($rows) ?>'),
      name: '<?php echo $name ?>',
      hasUniqueKey: <?php echo var_export($has_unique_keys) ?>,
      primaryKey: '<?php echo $primary_key ?>',
    };
    removeLastScriptTag();
  </script>
  <div class="default-container">
    <h3><?php echo ucfirst($name) ?></h3>
    <?php if (!$has_unique_keys): ?>
    <div class="spacer-v"></div>
    <div class="warning block-center">
      <div>
        <i class="material-icons">warning</i>
      </div>
      <div>
        <?php loc("table_no_unique_keys") ?>
      </div>
    </div>
    <div class="spacer-v"></div>
    <?php endif; ?>
    <div class="table-wrapper">
      <table class="table-view entry-view">
        <tbody>
        <?php if ($columns):
          echo "<tr>";
          foreach ($columns as $column) {
            $name = $column["Field"];
        ?>
            <th>
              <?php if ($name == $primary_key): ?>
              <button class="soft" title="<?php loc($primary_key_label) ?>">
                <i class="material-icons">vpn_key</i>
              </button>
              <?php endif; ?>
              <span <?php if ($name == $primary_key) echo "class='underline'" ?>>
                <?php echo $name ?>
              </span>
            </th>
        <?php
          }
          echo "</tr>";
        endif;

        if ($rows):
          foreach ($rows as $row) {
            echo "<tr class='table-entry'>";
            foreach ($row as $value) {
              echo "<td><div><span>";
              if ($value != "") {
                echo htmlentities($value);
              }
              else {
                echo "<i class='empty'>";
                loc("empty");
                echo "</i>";
              }
              echo "</span></div></td>";
            }
            echo "</tr>";
          }
        endif;
        ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="<?php echo count($columns) ?>">
              <div>
                <button type="button" class="soft floating-button" id="b-confirm-add" title="<?php loc("confirm") ?>" style="display: none;">
                  <i class="material-icons">done</i>
                </button>
                <button type="button" class="soft floating-button" id="b-cancel-add" title="<?php loc("cancel") ?>" style="display: none;">
                  <i class="material-icons">clear</i>
                </button>
                <button type="button" class="soft floating-button" id="b-add-entry" title="<?php loc("add_entry") ?>">
                  <i class="material-icons">add</i>
                </button>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
    <?php if (!$rows): ?>
    <p class="notice"><?php loc("table_empty") ?></p>
    <?php endif; ?>
  </div>
<?php
}

?>
