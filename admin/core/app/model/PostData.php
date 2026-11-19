<?php

#[\AllowDynamicProperties]
class PostData {
	public static $tablename = "post";

	public $id;
	public $category_id;
	public $client_id;
	public $code;
	public $name;
	public $short_name;
	public $description;
	public $offer_txt;
	public $image;
	public $file;
	public $link;
	public $downloads;
	public $is_featured;
	public $is_public;
	public $created_at;
	public $updated_at;

	// Navegación
	public $category_name = "";
	public $category_slug = "";
	public $author_name = "";
	public $views_count = 0;

	public function __construct() {
		$this->category_id = 1;
		$this->client_id = null;
		$this->code = "";
		$this->name = "";
		$this->short_name = "";
		$this->description = "";
		$this->offer_txt = "";
		$this->image = "";
		$this->file = "";
		$this->link = "";
		$this->downloads = 0;
		$this->is_featured = 0;
		$this->is_public = 1;
		$this->created_at = date("Y-m-d H:i:s");
		$this->updated_at = date("Y-m-d H:i:s");
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public function add(): int {
		$stmt = self::db()->prepare(
			"INSERT INTO " . self::$tablename . " (category_id, client_id, code, name, short_name, description, offer_txt, image, file, link, is_featured, is_public) " .
			"VALUES (:category_id, :client_id, :code, :name, :short_name, :description, :offer_txt, :image, :file, :link, :is_featured, :is_public)"
		);
		$stmt->execute([
			'category_id' => (int)$this->category_id,
			'client_id' => $this->client_id ?: null,
			'code' => $this->code ?: null,
			'name' => $this->name,
			'short_name' => $this->short_name ?: null,
			'description' => $this->description ?: null,
			'offer_txt' => $this->offer_txt ?: null,
			'image' => $this->image ?: null,
			'file' => $this->file ?: null,
			'link' => $this->link ?: null,
			'is_featured' => $this->is_featured ? 1 : 0,
			'is_public' => $this->is_public ? 1 : 0,
		]);
		$this->id = (int)self::db()->lastInsertId();
		return $this->id;
	}

	public function update(): bool {
		$stmt = self::db()->prepare(
			"UPDATE " . self::$tablename . " SET category_id = :category_id, client_id = :client_id, code = :code, name = :name, " .
			"short_name = :short_name, description = :description, offer_txt = :offer_txt, image = :image, file = :file, " .
			"link = :link, is_featured = :is_featured, is_public = :is_public WHERE id = :id"
		);
		return $stmt->execute([
			'category_id' => (int)$this->category_id,
			'client_id' => $this->client_id ?: null,
			'code' => $this->code ?: null,
			'name' => $this->name,
			'short_name' => $this->short_name ?: null,
			'description' => $this->description ?: null,
			'offer_txt' => $this->offer_txt ?: null,
			'image' => $this->image ?: null,
			'file' => $this->file ?: null,
			'link' => $this->link ?: null,
			'is_featured' => $this->is_featured ? 1 : 0,
			'is_public' => $this->is_public ? 1 : 0,
			'id' => (int)$this->id,
		]);
	}

	public static function incrementDownloads(int $id): void {
		$stmt = self::db()->prepare("UPDATE " . self::$tablename . " SET downloads = downloads + 1 WHERE id = :id");
		$stmt->execute(['id' => $id]);
	}

	private static function baseSelectSql(): string {
		return "SELECT p.*, c.name AS category_name, c.short_name AS category_slug, " .
			"COALESCE(CONCAT(cl.name, ' ', COALESCE(cl.lastname, '')), 'Administrador') AS author_name, " .
			"(SELECT COUNT(*) FROM post_view pv WHERE pv.post_id = p.id) AS views_count " .
			"FROM " . self::$tablename . " p " .
			"INNER JOIN category c ON c.id = p.category_id " .
			"LEFT JOIN client cl ON cl.id = p.client_id ";
	}

	public static function getById($id): ?self {
		$stmt = self::db()->prepare(self::baseSelectSql() . "WHERE p.id = :id LIMIT 1");
		$stmt->execute(['id' => (int)$id]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getAll(): array {
		$stmt = self::db()->query(self::baseSelectSql() . "ORDER BY p.id DESC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getByClientId(int $clientId): array {
		$stmt = self::db()->prepare(self::baseSelectSql() . "WHERE p.client_id = :cid ORDER BY p.id DESC");
		$stmt->execute(['cid' => $clientId]);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getPublics(): array {
		$stmt = self::db()->query(
			self::baseSelectSql() . "WHERE p.is_public = 1 AND c.is_active = 1 ORDER BY p.id DESC"
		);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getFeatureds(int $limit = 12): array {
		$stmt = self::db()->prepare(
			self::baseSelectSql() . "WHERE p.is_public = 1 AND p.is_featured = 1 AND c.is_active = 1 ORDER BY p.id DESC LIMIT :lim"
		);
		$stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getPublicsByCategory(int $categoryId): array {
		$stmt = self::db()->prepare(
			self::baseSelectSql() . "WHERE p.is_public = 1 AND p.category_id = :cid ORDER BY p.id DESC"
		);
		$stmt->execute(['cid' => $categoryId]);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function searchPublic(string $q): array {
		$like = '%' . $q . '%';
		$stmt = self::db()->prepare(
			self::baseSelectSql() .
			"WHERE p.is_public = 1 AND c.is_active = 1 AND (p.name LIKE :q1 OR p.code LIKE :q2 OR p.description LIKE :q3) " .
			"ORDER BY p.id DESC"
		);
		$stmt->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function delById($id): bool {
		$stmt = self::db()->prepare("DELETE FROM " . self::$tablename . " WHERE id = :id");
		return $stmt->execute(['id' => (int)$id]);
	}
}
