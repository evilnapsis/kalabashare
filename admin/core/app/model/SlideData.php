<?php

#[\AllowDynamicProperties]
class SlideData {
	public static $tablename = "slide";

	public $id;
	public $title;
	public $image;
	public $link;
	public $position;
	public $is_public;
	public $created_at;
	public $updated_at;

	public function __construct() {
		$this->title = "";
		$this->image = "";
		$this->link = "";
		$this->position = 1;
		$this->is_public = 1;
		$this->created_at = date("Y-m-d H:i:s");
		$this->updated_at = date("Y-m-d H:i:s");
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public function add(): int {
		$stmt = self::db()->prepare(
			"INSERT INTO " . self::$tablename . " (title, image, link, position, is_public) " .
			"VALUES (:title, :image, :link, :position, :is_public)"
		);
		$stmt->execute([
			'title' => $this->title ?: null,
			'image' => $this->image,
			'link' => $this->link ?: null,
			'position' => (int)$this->position,
			'is_public' => $this->is_public ? 1 : 0,
		]);
		$this->id = (int)self::db()->lastInsertId();
		return $this->id;
	}

	public function update(): bool {
		$stmt = self::db()->prepare(
			"UPDATE " . self::$tablename . " SET title = :title, image = :image, link = :link, " .
			"position = :position, is_public = :is_public WHERE id = :id"
		);
		return $stmt->execute([
			'title' => $this->title ?: null,
			'image' => $this->image,
			'link' => $this->link ?: null,
			'position' => (int)$this->position,
			'is_public' => $this->is_public ? 1 : 0,
			'id' => (int)$this->id,
		]);
	}

	public static function getById($id): ?self {
		$stmt = self::db()->prepare("SELECT * FROM " . self::$tablename . " WHERE id = :id LIMIT 1");
		$stmt->execute(['id' => (int)$id]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getAll(): array {
		$stmt = self::db()->query("SELECT * FROM " . self::$tablename . " ORDER BY position ASC, id DESC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getPublics(): array {
		$stmt = self::db()->query("SELECT * FROM " . self::$tablename . " WHERE is_public = 1 ORDER BY position ASC, id DESC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function delById($id): bool {
		$stmt = self::db()->prepare("DELETE FROM " . self::$tablename . " WHERE id = :id");
		return $stmt->execute(['id' => (int)$id]);
	}
}
