<?php

namespace App\Controller;

use ClientData;
use ViewEngine;
use Req;
use Response;

class ClientController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		ViewEngine::render('clients/index.html.twig', [
			'clients' => ClientData::getAll(),
		]);
	}

	public function new(): void {
		ViewEngine::render('clients/new.html.twig');
	}

	public function create(): void {
		$name = trim((string)Req::post('name', ''));
		$email = trim((string)Req::post('email', ''));
		$password = (string)Req::post('password', '');
		if ($name === '' || $email === '' || $password === '') {
			$_SESSION['error'] = 'Nombre, correo y contraseña son obligatorios.';
			Response::redirect($this->baseFolder . '/clients/new');
		}

		try {
			$c = new ClientData();
			$c->name = $name;
			$c->lastname = trim((string)Req::post('lastname', ''));
			$c->email = $email;
			$c->phone = trim((string)Req::post('phone', ''));
			$c->address = trim((string)Req::post('address', ''));
			$c->password_hash = password_hash($password, PASSWORD_DEFAULT);
			$c->is_active = isset($_POST['is_active']) ? 1 : 0;
			$c->add();
			$_SESSION['success'] = 'Cliente registrado correctamente.';
		} catch (\PDOException $e) {
			$_SESSION['error'] = 'Error al registrar cliente (correo duplicado).';
		}
		Response::redirect($this->baseFolder . '/clients');
	}

	public function edit(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$client = ClientData::getById($id);
		if (!$client) {
			$_SESSION['error'] = 'Cliente no encontrado.';
			Response::redirect($this->baseFolder . '/clients');
		}
		ViewEngine::render('clients/edit.html.twig', ['client' => $client]);
	}

	public function update(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$c = ClientData::getById($id);
		if (!$c) {
			$_SESSION['error'] = 'Cliente no encontrado.';
			Response::redirect($this->baseFolder . '/clients');
		}

		$c->name = trim((string)Req::post('name', ''));
		$c->lastname = trim((string)Req::post('lastname', ''));
		$c->email = trim((string)Req::post('email', ''));
		$c->phone = trim((string)Req::post('phone', ''));
		$c->address = trim((string)Req::post('address', ''));
		$c->is_active = isset($_POST['is_active']) ? 1 : 0;

		try {
			$c->update();
			$newPass = (string)Req::post('password', '');
			if ($newPass !== '') {
				$c->updatePassword(password_hash($newPass, PASSWORD_DEFAULT));
			}
			$_SESSION['updated'] = 'Cliente actualizado.';
		} catch (\PDOException $e) {
			$_SESSION['error'] = 'Error al actualizar cliente.';
		}
		Response::redirect($this->baseFolder . '/clients');
	}

	public function delete(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		ClientData::delById($id);
		$_SESSION['deleted'] = 'Cliente eliminado.';
		Response::redirect($this->baseFolder . '/clients');
	}
}
