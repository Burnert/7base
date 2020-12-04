<?php

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

?>
