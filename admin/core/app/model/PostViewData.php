<?php

#[\AllowDynamicProperties]
class PostViewData {
	public static $tablename = "post_view";

	public $id;
	public $post_id;
	public $ip_address;
	public $created_at;

	public function __construct() {
		$this->post_id = 0;
		$this->ip_address = "";
		$this->created_at = date("Y-m-d H:i:s");
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public static function record(int $postId, ?string $ip = null): void {
		$stmt = self::db()->prepare(
			"INSERT INTO " . self::$tablename . " (post_id, ip_address, created_at) VALUES (:pid, :ip, NOW())"
		);
		$stmt->execute(['pid' => $postId, 'ip' => $ip]);
	}

	public static function countTotal(): int {
		return (int)self::db()->query("SELECT COUNT(*) FROM " . self::$tablename)->fetchColumn();
	}

	public static function getLast30DaysChart(): array {
		$stmt = self::db()->query(
			"SELECT DATE(created_at) AS view_date, COUNT(*) AS total_views " .
			"FROM " . self::$tablename . " " .
			"WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) " .
			"GROUP BY DATE(created_at) ORDER BY view_date ASC"
		);
		return $stmt->fetchAll();
	}

	public static function getTopViewed(int $limit = 5): array {
		$stmt = self::db()->prepare(
			"SELECT p.id, p.name, p.code, COUNT(pv.id) AS total_views " .
			"FROM post p INNER JOIN post_view pv ON pv.post_id = p.id " .
			"GROUP BY p.id ORDER BY total_views DESC LIMIT :lim"
		);
		$stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}
