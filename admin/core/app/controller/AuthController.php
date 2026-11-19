<?php

namespace App\Controller;

use App\Service\AuthService;
use ConfigurationData;
use ViewEngine;
use Req;
use Response;

class AuthController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function loginForm(): void {
		if (AuthService::check()) {
			Response::redirect($this->baseFolder . '/home');
		}
		$cfg = ConfigurationData::getMap();
		ViewEngine::render('auth/login.html.twig', [
			'site_title' => $cfg['general_main_title'] ?? 'KATALOG',
		]);
	}

	public function login(): void {
		$login = trim((string)Req::post('username', ''));
		$password = (string)Req::post('password', '');

		if ($login === '' || $password === '') {
			$_SESSION['error'] = 'Ingresa tu usuario y contraseña.';
			Response::redirect($this->baseFolder . '/login');
		}

		$user = AuthService::attempt($login, $password);
		if (!$user) {
			$_SESSION['error'] = 'Usuario o contraseña incorrectos.';
			Response::redirect($this->baseFolder . '/login');
		}

		$_SESSION['success'] = 'Bienvenido, ' . $user->getFullName();
		Response::redirect($this->baseFolder . '/home');
	}

	public function logout(): void {
		AuthService::logout();
		Response::redirect($this->baseFolder . '/login');
	}
}
