<?php

require_once("./php/components/search_window.php");

function table_view($name, $rows, $columns) {
  global $search_column, $search_query;
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

  $foreign_column_names = array_map(valueMapper("Column"), $foreign_keys);

  $foreign_columns = [];
  foreach ($foreign_keys as $key) {
    // $condition_str = "`" . $key["RefColumn"] . "`" . " IN (";
    // // Select used IDs
    // foreach ($rows as $index => $row) {
    //   $condition_str .= $row[$key["Column"]];
    //   if ($index < count($rows) - 1) {
    //     $condition_str .= ", ";
    //   }
    // }
    // $condition_str .= ")";
    $foreign_columns[$key["Column"]] = DatabaseManager::get()->select_from_table($key["RefTable"], null, null);
  }

  search_window($name, $columns);
?>
  <script>
    const currentTable = {
      name: '<?php echo addslashes($name) ?>',
      columns: JSON.parse('<?php echo addslashes(json_encode($columns)) ?>'),
      rows: JSON.parse('<?php echo addslashes(json_encode($rows)) ?>'),
      hasUniqueKey: <?php echo var_export($has_unique_keys) ?>,
      primaryKey: '<?php echo $primary_key ?>',
      foreignColumns: JSON.parse('<?php echo addslashes(json_encode($foreign_columns)) ?>'),
    };
    removeLastScriptTag();
  </script>
  <div class="default-container">
    <div class="horiz f-center">
      <h3><?php echo ucfirst($name) ?></h3>
      <button class="soft search" title="<?php loc("search") ?>">
        <i class="material-icons">search</i>
      </button>
    </div>
    <!-- No unique keys warning -->
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
    <!-- Showing only queried entries info -->
    <?php if ($search_column && $search_query): ?>
    <div class="spacer-v"></div>
    <div class="info block-center">
      <div>
        <i class="material-icons">info</i>
      </div>
      <div>
        <?php 
        loc("showing_only_queried");
        echo "<br>";
        loc("search_column_name");
        echo " &ndash; \"<b>$search_column</b>\"";
        echo "<br>";
        loc("search_query");
        echo " &ndash; \"$search_query\"";
        ?>
      </div>
      <div>
        <a href="index.php?table=<?php echo $name ?>" class="button soft nobg" title="<?php loc("search_back") ?>">
          <i class="material-icons">settings_backup_restore</i>
        </a>
      </div>
    </div>
    <div class="spacer-v"></div>
    <?php endif; ?>
    <!-- Table view -->
    <div class="table-wrapper">
      <table class="table-view entry-view">
        <tbody>
        <?php if ($columns):
          echo "<tr class='columns'>";
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
              echo "<td><div><span class='value'>";
              if ($value != "") {
                // If column has foreign key
                if (array_search($column, $foreign_column_names) !== false) {
                  $foreign_column = $foreign_columns[$column];
                  $ref_column = getForeignKeyByColumn($foreign_keys, $column)["RefColumn"];
                  $foreign_row = getForeignRowByColumnValue($foreign_column, $ref_column, $value);
                  if (isset($foreign_row["name"])) {
                    echo htmlentities($foreign_row["name"]);
                  }
                  else {
                    echo "<b>" . htmlentities($value) . "</b>";
                  }
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
