<?php

#[\AllowDynamicProperties]
class UserData {
	public static $tablename = "`user`";

	public $id;
	public $name;
	public $lastname;
	public $username;
	public $email;
	public $password_hash;
	public $role;
	public $is_active;
	public $created_at;
	public $updated_at;

	public function __construct() {
		$this->name = "";
		$this->lastname = "";
		$this->username = "";
		$this->email = "";
		$this->password_hash = "";
		$this->role = "admin";
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
			"INSERT INTO " . self::$tablename . " (name, lastname, username, email, password_hash, role, is_active) " .
			"VALUES (:name, :lastname, :username, :email, :password_hash, :role, :is_active)"
		);
		$stmt->execute([
			'name' => $this->name,
			'lastname' => $this->lastname ?: null,
			'username' => $this->username ?: null,
			'email' => $this->email,
			'password_hash' => $this->password_hash,
			'role' => $this->role ?: 'admin',
			'is_active' => $this->is_active ? 1 : 0,
		]);
		$this->id = (int)self::db()->lastInsertId();
		return $this->id;
	}

	public function update(): bool {
		$stmt = self::db()->prepare(
			"UPDATE " . self::$tablename . " SET name = :name, lastname = :lastname, username = :username, " .
			"email = :email, role = :role, is_active = :is_active WHERE id = :id"
		);
		return $stmt->execute([
			'name' => $this->name,
			'lastname' => $this->lastname ?: null,
			'username' => $this->username ?: null,
			'email' => $this->email,
			'role' => $this->role ?: 'admin',
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

	public static function getByLogin(string $login): ?self {
		$stmt = self::db()->prepare(
			"SELECT * FROM " . self::$tablename . " WHERE (username = :l1 OR email = :l2) AND is_active = 1 LIMIT 1"
		);
		$stmt->execute(['l1' => $login, 'l2' => $login]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getAll(): array {
		$stmt = self::db()->query("SELECT * FROM " . self::$tablename . " ORDER BY id DESC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function delById($id): bool {
		$stmt = self::db()->prepare("DELETE FROM " . self::$tablename . " WHERE id = :id");
		return $stmt->execute(['id' => (int)$id]);
	}
}
