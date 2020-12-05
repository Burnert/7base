<?php

function table_view($rows, $columns) {
?>
  <script>
    const currentTableColumns = JSON.parse('<?php echo json_encode($columns) ?>');
    const currentTableRows = JSON.parse('<?php echo json_encode($rows) ?>');
    Array.from(document.querySelectorAll('script')).reverse()[0].remove();
  </script>
  <div class="default-container">
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
          echo "<td><div>";
          if ($value) {
            echo $value;
          }
          else {
            echo "<i>";
            loc("empty");
            echo "</i>";
          }
          echo "</div></td>";
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
            <button type="button" class="soft" id="b-confirm-add" title="<?php loc("confirm") ?>" style="display: none;">
              <i class="material-icons">done</i>
            </button>
            <button type="button" class="soft" id="b-cancel-add" title="<?php loc("cancel") ?>" style="display: none;">
              <i class="material-icons">clear</i>
            </button>
            <button type="button" class="soft" id="b-add-entry" title="<?php loc("add_entry") ?>">
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
