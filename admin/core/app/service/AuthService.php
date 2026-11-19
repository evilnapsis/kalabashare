<?php

namespace App\Service;

use UserData;
use Session;

class AuthService {
	public static function check(): bool {
		return Session::issetUID();
	}

	public static function user(): ?UserData {
		$uid = Session::getUID();
		if (!$uid) {
			return null;
		}
		return UserData::getById((int)$uid);
	}

	public static function attempt(string $login, string $password): ?UserData {
		$user = UserData::getByLogin(trim($login));
		if (!$user || !password_verify($password, $user->password_hash)) {
			return null;
		}
		Session::setUID($user->id);
		$_SESSION['user_role'] = $user->role;
		return $user;
	}

	public static function logout(): void {
		Session::unsetUID();
		unset($_SESSION['user_role']);
		session_destroy();
	}
}
