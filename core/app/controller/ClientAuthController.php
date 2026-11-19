<?php

namespace App\Controller;

use ClientData;
use ViewEngine;
use Req;
use Response;

class ClientAuthController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function loginForm(): void {
		if (!empty($_SESSION['client_id'])) {
			Response::redirect($this->baseFolder . '/client');
		}
		ViewEngine::render('client/login.html.twig');
	}

	public function login(): void {
		$email = trim((string)Req::post('email', ''));
		$password = (string)Req::post('password', '');

		$client = ClientData::getByEmail($email);
		if (!$client || !password_verify($password, $client->password_hash)) {
			$_SESSION['error'] = 'Correo o contraseña incorrectos.';
			Response::redirect($this->baseFolder . '/login');
		}

		$_SESSION['client_id'] = $client->id;
		$_SESSION['success'] = 'Bienvenido, ' . $client->getFullName();
		Response::redirect($this->baseFolder . '/client');
	}

	public function registerForm(): void {
		if (!empty($_SESSION['client_id'])) {
			Response::redirect($this->baseFolder . '/client');
		}
		ViewEngine::render('client/register.html.twig');
	}

	public function register(): void {
		$name = trim((string)Req::post('name', ''));
		$lastname = trim((string)Req::post('lastname', ''));
		$email = trim((string)Req::post('email', ''));
		$phone = trim((string)Req::post('phone', ''));
		$password = (string)Req::post('password', '');

		if ($name === '' || $email === '' || $password === '') {
			$_SESSION['error'] = 'Nombre, correo y contraseña son obligatorios.';
			Response::redirect($this->baseFolder . '/register');
		}

		if (ClientData::getByEmail($email)) {
			$_SESSION['error'] = 'Este correo ya está registrado.';
			Response::redirect($this->baseFolder . '/register');
		}

		$c = new ClientData();
		$c->name = $name;
		$c->lastname = $lastname;
		$c->email = $email;
		$c->phone = $phone;
		$c->password_hash = password_hash($password, PASSWORD_DEFAULT);
		$c->is_active = 1;
		$c->add();

		$_SESSION['client_id'] = $c->id;
		$_SESSION['success'] = 'Cuenta creada correctamente. Ya puedes compartir archivos.';
		Response::redirect($this->baseFolder . '/client');
	}

	public function logout(): void {
		unset($_SESSION['client_id']);
		Response::redirect($this->baseFolder . '/');
	}
}
