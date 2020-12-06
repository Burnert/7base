<?php

class DatabaseManager {
  private $link;
  private static $dbmanager;

  public function connect($host, $login, $password) {
    $this->link = @mysqli_connect($host, $login, $password);
    @mysqli_set_charset($this->link, "UTF-8");
  }

  public function select_database($database) {
    if ($this->link) {
      @mysqli_select_db($this->link, $database);
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

  public function create_table($table) {
    if ($this->link) {
      foreach ($table as $column => $data) {
        var_dump($column);
        foreach ($data as $name => $property) {
          
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
    $condition = mysqli_real_escape_string($this->link, $name);

    $query = "SELECT ";
    if ($values && is_array($values)) {
      for ($i = 0; $i < count($values); $i++) {
        $value = mysqli_real_escape_string($this->link, $values[$i]);

        $query .= $value;
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

    if ($entries && is_array($entries)) {
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