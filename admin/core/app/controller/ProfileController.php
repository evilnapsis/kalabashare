<?php

namespace App\Controller;

use App\Service\AuthService;
use ViewEngine;
use Req;
use Response;

class ProfileController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		ViewEngine::render('profile/index.html.twig', [
			'user' => AuthService::user(),
		]);
	}

	public function update(): void {
		$user = AuthService::user();
		if (!$user) {
			Response::redirect($this->baseFolder . '/login');
		}

		$user->name = trim((string)Req::post('name', $user->name));
		$user->lastname = trim((string)Req::post('lastname', $user->lastname));
		$user->email = trim((string)Req::post('email', $user->email));
		$user->update();

		$newPass = (string)Req::post('password', '');
		if ($newPass !== '') {
			$user->updatePassword(password_hash($newPass, PASSWORD_DEFAULT));
		}

		$_SESSION['updated'] = 'Perfil actualizado.';
		Response::redirect($this->baseFolder . '/profile');
	}
}
