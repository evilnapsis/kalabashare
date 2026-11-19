<?php

namespace App\Controller;

use ConfigurationData;
use ViewEngine;
use Response;

class ConfigurationController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		ViewEngine::render('settings/index.html.twig', [
			'configs' => ConfigurationData::getAll(),
		]);
	}

	public function update(): void {
		$configs = ConfigurationData::getAll();
		foreach ($configs as $cfg) {
			if (isset($_POST[$cfg->name])) {
				ConfigurationData::updateValue($cfg->name, trim((string)$_POST[$cfg->name]));
			}
		}
		$_SESSION['updated'] = 'Configuración actualizada correctamente.';
		Response::redirect($this->baseFolder . '/settings');
	}
}
