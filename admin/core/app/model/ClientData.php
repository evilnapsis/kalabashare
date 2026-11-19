<?php

#[\AllowDynamicProperties]
class ClientData {
	public static $tablename = "client";

	public $id;
	public $name;
	public $lastname;
	public $email;
	public $phone;
	public $address;
	public $password_hash;
	public $is_active;
	public $created_at;
	public $updated_at;

	// Navegación
	public $post_count = 0;

	public function __construct() {
		$this->name = "";
		$this->lastname = "";
		$this->email = "";
		$this->phone = "";
		$this->address = "";
		$this->password_hash = "";
		$this->is_active = 1;
		$this->created_at = date("Y-m-d H:i:s");
		$this->updated_at = date("Y-m-d H:i:s");
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public function getFullName(): string {
		return trim($this->name . ' ' . ($this->lastname ?? ''));
	}

	public function add(): int {
		$stmt = self::db()->prepare(
			"INSERT INTO " . self::$tablename . " (name, lastname, email, phone, address, password_hash, is_active) " .
			"VALUES (:name, :lastname, :email, :phone, :address, :password_hash, :is_active)"
		);
		$stmt->execute([
			'name' => $this->name,
			'lastname' => $this->lastname ?: null,
			'email' => $this->email,
			'phone' => $this->phone ?: null,
			'address' => $this->address ?: null,
			'password_hash' => $this->password_hash,
			'is_active' => $this->is_active ? 1 : 0,
		]);
		$this->id = (int)self::db()->lastInsertId();
		return $this->id;
	}

	public function update(): bool {
		$stmt = self::db()->prepare(
			"UPDATE " . self::$tablename . " SET name = :name, lastname = :lastname, email = :email, " .
			"phone = :phone, address = :address, is_active = :is_active WHERE id = :id"
		);
		return $stmt->execute([
			'name' => $this->name,
			'lastname' => $this->lastname ?: null,
			'email' => $this->email,
			'phone' => $this->phone ?: null,
			'address' => $this->address ?: null,
			'is_active' => $this->is_active ? 1 : 0,
			'id' => (int)$this->id,
		]);
	}

	public function updatePassword(string $newHash): bool {
		$stmt = self::db()->prepare("UPDATE " . self::$tablename . " SET password_hash = :ph WHERE id = :id");
		return $stmt->execute(['ph' => $newHash, 'id' => (int)$this->id]);
	}

	public static function getById($id): ?self {
		$stmt = self::db()->prepare("SELECT * FROM " . self::$tablename . " WHERE id = :id LIMIT 1");
		$stmt->execute(['id' => (int)$id]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getByEmail(string $email): ?self {
		$stmt = self::db()->prepare("SELECT * FROM " . self::$tablename . " WHERE email = :email AND is_active = 1 LIMIT 1");
		$stmt->execute(['email' => trim($email)]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getAll(): array {
		$stmt = self::db()->query(
			"SELECT c.*, (SELECT COUNT(*) FROM post p WHERE p.client_id = c.id) AS post_count " .
			"FROM " . self::$tablename . " c ORDER BY c.id DESC"
		);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function delById($id): bool {
		$stmt = self::db()->prepare("DELETE FROM " . self::$tablename . " WHERE id = :id");
		return $stmt->execute(['id' => (int)$id]);
	}
}
