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

	public function get_all_tables() {
		if ($this->link) {
			$query = "SHOW TABLES;";
			$result = @mysqli_query($this->link, $query);
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
		$result = @mysqli_query($this->link, $query);
		$rows = @mysqli_fetch_all($result, MYSQLI_ASSOC);
		return $rows;
	}

	public function select_from_table($name, $values = null, $condition = null) {
		$query = "SELECT ";
		if ($values && is_array($values)) {
			for ($i = 0; $i < count($values); $i++) {
				$query .= $values[$i];
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

		$result = @mysqli_query($this->link, $query);
		$rows = @mysqli_fetch_all($result, MYSQLI_ASSOC);
		return $rows;
	}

	public function add_entries($entries) {
		foreach ($entries as $entry) {
			var_dump($entry);
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