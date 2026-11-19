<?php

#[\AllowDynamicProperties]
class CategoryData {
	public static $tablename = "category";

	public $id;
	public $name;
	public $short_name;
	public $in_home;
	public $in_menu;
	public $is_active;
	public $created_at;
	public $updated_at;

	// Navegación
	public $post_count = 0;

	public function __construct() {
		$this->name = "";
		$this->short_name = "";
		$this->in_home = 0;
		$this->in_menu = 1;
		$this->is_active = 1;
		$this->created_at = date("Y-m-d H:i:s");
		$this->updated_at = date("Y-m-d H:i:s");
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public function add(): int {
		$stmt = self::db()->prepare(
			"INSERT INTO " . self::$tablename . " (name, short_name, in_home, in_menu, is_active) " .
			"VALUES (:name, :short_name, :in_home, :in_menu, :is_active)"
		);
		$stmt->execute([
			'name' => $this->name,
			'short_name' => $this->short_name,
			'in_home' => $this->in_home ? 1 : 0,
			'in_menu' => $this->in_menu ? 1 : 0,
			'is_active' => $this->is_active ? 1 : 0,
		]);
		$this->id = (int)self::db()->lastInsertId();
		return $this->id;
	}

	public function update(): bool {
		$stmt = self::db()->prepare(
			"UPDATE " . self::$tablename . " SET name = :name, short_name = :short_name, " .
			"in_home = :in_home, in_menu = :in_menu, is_active = :is_active WHERE id = :id"
		);
		return $stmt->execute([
			'name' => $this->name,
			'short_name' => $this->short_name,
			'in_home' => $this->in_home ? 1 : 0,
			'in_menu' => $this->in_menu ? 1 : 0,
			'is_active' => $this->is_active ? 1 : 0,
			'id' => (int)$this->id,
		]);
	}

	public static function getById($id): ?self {
		$stmt = self::db()->prepare("SELECT * FROM " . self::$tablename . " WHERE id = :id LIMIT 1");
		$stmt->execute(['id' => (int)$id]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getBySlug(string $slug): ?self {
		$stmt = self::db()->prepare("SELECT * FROM " . self::$tablename . " WHERE short_name = :slug AND is_active = 1 LIMIT 1");
		$stmt->execute(['slug' => $slug]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getAll(): array {
		$stmt = self::db()->query(
			"SELECT c.*, (SELECT COUNT(*) FROM post p WHERE p.category_id = c.id) AS post_count " .
			"FROM " . self::$tablename . " c ORDER BY c.name ASC"
		);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getPublics(): array {
		$stmt = self::db()->query("SELECT * FROM " . self::$tablename . " WHERE is_active = 1 ORDER BY name ASC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getMenuCategories(): array {
		$stmt = self::db()->query("SELECT * FROM " . self::$tablename . " WHERE is_active = 1 AND in_menu = 1 ORDER BY name ASC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function delById($id): bool {
		$stmt = self::db()->prepare("DELETE FROM " . self::$tablename . " WHERE id = :id");
		return $stmt->execute(['id' => (int)$id]);
	}
}
