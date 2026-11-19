<?php

class Response {
	public static function json($data, int $statusCode = 200): void {
		http_response_code($statusCode);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data);
		exit;
	}

	public static function redirect(string $url, int $statusCode = 302): void {
		http_response_code($statusCode);
		header('Location: ' . $url);
		exit;
	}
}
