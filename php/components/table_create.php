<?php

function table_create() {
?>
  <div class="create-container">
    <form action="./php/database/create_table.php" method="POST" autocomplete="off">
      <div class="vert block-center fit">
        <div class="input-generic">
          <label for="cr-name"><?php loc("table_name") ?></label>
          <input type="text" name="name" id="cr-name">
        </div>
        <div class="spacer-v"></div>
        <h3><?php loc("columns") ?></h3>
      </div>
      <div class="table-wrapper">
        <table class="create-table table-view">
          <tbody>
            <tr>
              <th><?php loc("name") ?></th>
              <th><?php loc("type") ?></th>
              <th><?php loc("length"); echo "/"; loc("values") ?></th>
              <th><?php loc("default_value") ?></th>
              <th><?php loc("nullable") ?></th>
              <th><?php loc("index") ?></th>
              <th><?php loc("auto_increment") ?></th>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="7">
                <div>
                  <button type="button" class="soft" id="b-add-column" title="<?php loc("add_column") ?>">
                    <i class="material-icons">add</i>
                  </button>
                </div>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </form>
  </div>
<?php
}

?>