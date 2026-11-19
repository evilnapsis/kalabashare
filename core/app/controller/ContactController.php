<?php

namespace App\Controller;

use ViewEngine;
use Req;
use Response;

class ContactController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		ViewEngine::render('contact/index.html.twig');
	}

	public function send(): void {
		$name = trim((string)Req::post('name', ''));
		$email = trim((string)Req::post('email', ''));
		$message = trim((string)Req::post('message', ''));

		if ($name === '' || $email === '' || $message === '') {
			$_SESSION['error'] = 'Por favor completa nombre, correo y mensaje.';
			Response::redirect($this->baseFolder . '/contact');
		}

		$_SESSION['success'] = 'Gracias por contactarnos, ' . $name . '. Hemos recibido tu mensaje.';
		Response::redirect($this->baseFolder . '/contact');
	}
}
