<?php

require_once("./php/components/search_window.php");

function table_view($name, $rows, $columns) {
  // Get all unique keys
  $unique_keys = array_map(function($value) {
    return $value["Field"];
  }, array_filter($columns, function($column) {
    return $column["Key"] == "UNI";
  }));
  // Get all index keys
  $index_keys = array_map(function($value) {
    return $value["Field"];
  }, array_filter($columns, function($column) {
    return $column["Key"] == "MUL";
  }));
  // Get primary key
  $primary_key = array_reduce(array_filter($columns, function($column) {
    return $column["Key"] == "PRI";
  }), function($carry, $item) {
    return $item["Field"];
  });
  $has_unique_keys = !empty($unique_keys) || $primary_key;
  $primary_key_label = "key_primary";
  $unique_key_label = "key_unique";
  $index_key_label = "key_index";

  $foreign_keys = DatabaseManager::get()->get_table_foreign_keys($name);

  function getForeignKeyByColumn($f_keys, $column) {
    return array_reduce(array_filter($f_keys, function($key) use ($column) {
      return $key["Column"] == $column;
    }), function($carry, $item) {
      return $item;
    });
  }

  function getForeignRowByColumnValue($f_values, $column, $value) {
    return array_reduce(array_filter($f_values, function($f_val) use ($column, $value) {
      return $f_val[$column] == $value;
    }), function($carry, $item) {
      return $item;
    });
  }

  function valueMapper($name) {
    return function($value) use ($name) {
      return $value[$name];
    };
  }

  $foreign_columns = array_map(valueMapper("Column"), $foreign_keys);

  $foreign_values = [];
  foreach ($foreign_keys as $key) {
    $condition_str = "`" . $key["RefColumn"] . "`" . " IN (";
    // Select used IDs
    foreach ($rows as $index => $row) {
      $condition_str .= $row[$key["Column"]];
      if ($index < count($rows) - 1) {
        $condition_str .= ", ";
      }
    }
    $condition_str .= ")";
    $foreign_values[$key["Column"]] = DatabaseManager::get()->select_from_table($key["RefTable"], null, $condition_str);
  }

  search_window($columns);
?>
  <script>
    const currentTable = {
      columns: JSON.parse('<?php echo addslashes(json_encode($columns)) ?>'),
      rows: JSON.parse('<?php echo addslashes(json_encode($rows)) ?>'),
      name: '<?php echo addslashes($name) ?>',
      hasUniqueKey: <?php echo var_export($has_unique_keys) ?>,
      primaryKey: '<?php echo $primary_key ?>',
    };
    removeLastScriptTag();
  </script>
  <div class="default-container">
    <div class="horiz f-center">
      <h3><?php echo ucfirst($name) ?></h3>
      <button class="soft search">
        <i class="material-icons">search</i>
      </button>
    </div>
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
              <button class="soft primary-key" title="<?php loc($primary_key_label) ?>">
                <i class="material-icons">vpn_key</i>
              </button>
              <?php endif; ?>

              <?php if (array_search($name, $unique_keys) !== false): ?>
              <button class="soft" title="<?php loc($unique_key_label) ?>">
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
            foreach ($row as $column => $value) {
              echo "<td><div><span>";
              if ($value != "") {
                // If column has foreign key
                if (array_search($column, $foreign_columns) !== false) {
                  $foreign_column = $foreign_values[$column];
                  $ref_column = getForeignKeyByColumn($foreign_keys, $column)["RefColumn"];
                  $foreign_row = getForeignRowByColumnValue($foreign_column, $ref_column, $value);
                  echo htmlentities($foreign_row["name"]);
                }
                else {
                  echo htmlentities($value);
                }
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
