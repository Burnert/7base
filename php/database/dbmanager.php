<?php

class DatabaseManager {
  private $link;
  private $database_name;
  private static $dbmanager;

  public function connect($host, $login, $password) {
    $this->link = @mysqli_connect($host, $login, $password);
    @mysqli_set_charset($this->link, "UTF-8");
  }

  public function close() {
    if ($this->link) {
      @mysqli_close($this->link);
    }
  }

  public function select_database($database) {
    if ($this->link) {
      @mysqli_select_db($this->link, $database);
      $this->database_name = $database;
    }
  }

  private function prepare_query($query) {
    if (!$this->link) return null;
    $stmt = @mysqli_prepare($this->link, $query);
    return $stmt;
  }

  private function query($query) {
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  public function get_user($username, $password) {
    if ($this->link) {
      $query = "SELECT `Host`, `User`, `Password` FROM `mysql`.`user` WHERE `User` = '$username' AND `Password` = PASSWORD('$password')";
      $result = $this->query($query);
      return @mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
  }

  public function get_all_databases() {
    if ($this->link) {
      $query = "SHOW DATABASES;";
      $result = $this->query($query);
      $array = @mysqli_fetch_all($result, MYSQLI_NUM);
      return array_map(function($value) {
        return $value[0];
      }, $array);
    }
  }

  public function get_all_tables() {
    if ($this->link) {
      $query = "SHOW TABLES;";
      $result = $this->query($query);
      $array = @mysqli_fetch_all($result, MYSQLI_NUM);
      return array_map(function($value) {
        return $value[0];
      }, $array);
    }
  }

  public function get_table_foreign_keys($name) {
    if ($this->link) {
      $query = "SELECT 
      `CONSTRAINT_NAME` AS `Name`, 
      `COLUMN_NAME` AS `Column`, 
      `REFERENCED_TABLE_SCHEMA` AS `RefDatabase`, 
      `REFERENCED_TABLE_NAME` AS `RefTable`, 
      `REFERENCED_COLUMN_NAME` AS `RefColumn` 
      FROM `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` 
      WHERE `TABLE_SCHEMA` = '$this->database_name' 
      AND `TABLE_NAME` = '$name' 
      AND `REFERENCED_TABLE_SCHEMA` IS NOT NULL;";
      $result = $this->query($query);
      $keys = @mysqli_fetch_all($result, MYSQLI_ASSOC);
      return $keys;
    }
  }

  public function create_table($name, $table) {
    if ($this->link) {
      foreach ($table as $column => $data) {
        var_dump($column);
        foreach ($data as $type => $property) {
          
        }
      }
    }
  }

  public function describe_table($name) {
    $query = "DESCRIBE `$name`;";
    $result = $this->query($query);
    $rows = @mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $rows;
  }

  public function select_from_table($name, $values = null, $condition = null) {
    $name = mysqli_real_escape_string($this->link, $name);

    $query = "SELECT ";
    if ($values && is_array($values)) {
      for ($i = 0; $i < count($values); $i++) {
        $value = mysqli_real_escape_string($this->link, $values[$i]);

        $query .= "`$value`";
        if ($i < count($values) - 1) {
          $query .= ", ";
        }
      }
    }
    else {
      $query .= "*";
    }
    $query .= " FROM `$name`";
    if ($condition && is_string($condition)) {
      $query .= " WHERE $condition";
    }
    $query .= ";";
    $result = $this->query($query);
    $rows = @mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $rows;
  }

  public function add_entries($name, $columns, $entries) {
    $name = mysqli_real_escape_string($this->link, $name);

    if ($entries && is_array($entries) &&
        $columns && is_array($columns)) {
      $query = "INSERT INTO `$name` (";
      // Column names
      foreach ($columns as $index => $column) {
        $column = mysqli_real_escape_string($this->link, $column);

        $query .= "`$column`";
        if ($index < count($columns) - 1) {
          $query .= ", ";
        }
      }
      $query .= ") VALUES ";
      // Entries
      foreach ($entries as $index => $entry) {
        $query .= "(";
        $n = 0;
        $entry_array = (array) $entry;
        foreach ($entry_array as $column => $value) {
          if ($value != "") {
            $value = mysqli_real_escape_string($this->link, $value);

            $query .= "'$value'";
          }
          else {
            $query .= "NULL";
          }
          if ($n++ < count($entry_array) - 1) {
            $query .= ", ";
          }
        }
        $query .= ")";
        if ($index < count($entries) - 1) {
          $query .= ", ";
        }
      }
      $query .= ";";
      $result = $this->query($query);
      echo var_export($result);
    }
  }

  public function delete_entries_unique($name, $key, $entries) {
    $name = mysqli_real_escape_string($this->link, $name);

    if ($key && $entries && is_array($entries)) {
      $query = "DELETE FROM `$name` WHERE $key IN (";

      foreach ($entries as $index => $value) {
        $query .= "'$value'";
        if ($index < count($entries) - 1) {
          $query .= ", ";
        }
      }
      $query .= ");";
      $result = $this->query($query);
      echo var_export($result);
    }
  }

  public function update_entries_unique($name, $primary_key, $entries) {
    $name = mysqli_real_escape_string($this->link, $name);

    if ($primary_key && $entries && is_array($entries)) {
      foreach ($entries["old"] as $index => $entry) {
        $query = "";
        $found_different_field = false;
        foreach ($entry as $key => $value) {
          $new_value = ((array) $entries["new"][$index])[$key];
          if ($new_value != $value) {
            if ($found_different_field) {
              $query .= ", ";
            }
            else {
              $query .= "UPDATE `$name` SET ";
            }
            $query .= "`$key`" . " = " . "'$new_value'";
            $found_different_field = true;
          }
        }
        if ($found_different_field) {
          $old_key = ((array) $entries["old"][$index])[$primary_key];
          $query .= " WHERE `$primary_key` = '$old_key';";
          $result = $this->query($query);
          echo var_export($result);
        }
      }
    }
  }

  public static function create() {
    if (!self::$dbmanager) {
      self::$dbmanager = new DatabaseManager();
    }
  }

  public static function get() {
    return self::$dbmanager;
  }
}

?>