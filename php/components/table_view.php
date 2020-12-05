<?php

function table_view($name, $rows, $columns) {
  $has_unique_key = false;
?>
  <script>
    const currentTable = {
      columns: JSON.parse('<?php echo json_encode($columns) ?>'),
      rows: JSON.parse('<?php echo json_encode($rows) ?>'),
      name: '<?php echo $name ?>',
      hasUniqueKey: <?php echo var_export($has_unique_key) ?>,
    };
    removeLastScriptTag();
  </script>
  <div class="default-container">
    <h3><?php echo ucfirst($name) ?></h3>
    <div class="table-wrapper">
      <table class="table-view entry-view">
        <tbody>
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
            echo "<tr class='table-entry'>";
            foreach ($row as $value) {
              echo "<td><div><span>";
              if ($value) {
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
