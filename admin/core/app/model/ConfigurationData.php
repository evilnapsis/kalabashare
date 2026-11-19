<?php

#[\AllowDynamicProperties]
class ConfigurationData {
	public static $tablename = "configuration";

	public $id;
	public $name;
	public $label;
	public $kind;
	public $val;
	public $created_at;
	public $updated_at;

	public function __construct() {
		$this->name = "";
		$this->label = "";
		$this->kind = "text";
		$this->val = "";
		$this->created_at = date("Y-m-d H:i:s");
		$this->updated_at = date("Y-m-d H:i:s");
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public static function getAll(): array {
		$stmt = self::db()->query("SELECT * FROM " . self::$tablename . " ORDER BY id ASC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getMap(): array {
		$rows = self::getAll();
		$map = [];
		foreach ($rows as $r) {
			$map[$r->name] = $r->val;
		}
		return $map;
	}

	public static function updateValue(string $name, ?string $val): bool {
		$stmt = self::db()->prepare("UPDATE " . self::$tablename . " SET val = :v WHERE name = :n");
		return $stmt->execute(['v' => $val, 'n' => $name]);
	}
}
