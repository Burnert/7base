<?php

function table_view($rows, $columns) {
  global $lang;
?>
  <div class="table-wrapper">
  <table class="table-view">
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
        echo "<tr>";
        foreach ($row as $value) {
          echo "<td>";
          if ($value) {
            echo $value;
          }
          else {
            echo "<i>" . $lang["empty"] . "</i>";
          }
          echo "</td>";
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
            <button type="button" class="soft" id="b-confirm-add" title="<?php loc("confirm") ?>">
              <i class="material-icons">done</i>
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
<?php
}

?>
