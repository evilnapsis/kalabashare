<?php

namespace App\Controller;

use CategoryData;
use ViewEngine;
use Req;
use Response;

class CategoryController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		ViewEngine::render('categories/index.html.twig', [
			'categories' => CategoryData::getAll(),
		]);
	}

	public function new(): void {
		ViewEngine::render('categories/new.html.twig');
	}

	private function slugify(string $text): string {
		$slug = strtolower(trim($text));
		$slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
		return trim((string)$slug, '-');
	}

	public function create(): void {
		$name = trim((string)Req::post('name', ''));
		$shortName = trim((string)Req::post('short_name', '')) ?: $this->slugify($name);
		if ($name === '' || $shortName === '') {
			$_SESSION['error'] = 'El nombre de la categoría es obligatorio.';
			Response::redirect($this->baseFolder . '/categories/new');
		}

		try {
			$c = new CategoryData();
			$c->name = $name;
			$c->short_name = $shortName;
			$c->in_home = isset($_POST['in_home']) ? 1 : 0;
			$c->in_menu = isset($_POST['in_menu']) ? 1 : 0;
			$c->is_active = isset($_POST['is_active']) ? 1 : 0;
			$c->add();
			$_SESSION['success'] = 'Categoría creada correctamente.';
		} catch (\PDOException $e) {
			$_SESSION['error'] = 'No se pudo crear la categoría (slug duplicado).';
		}
		Response::redirect($this->baseFolder . '/categories');
	}

	public function edit(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$cat = CategoryData::getById($id);
		if (!$cat) {
			$_SESSION['error'] = 'Categoría no encontrada.';
			Response::redirect($this->baseFolder . '/categories');
		}
		ViewEngine::render('categories/edit.html.twig', ['category' => $cat]);
	}

	public function update(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$c = CategoryData::getById($id);
		if (!$c) {
			$_SESSION['error'] = 'Categoría no encontrada.';
			Response::redirect($this->baseFolder . '/categories');
		}

		$c->name = trim((string)Req::post('name', ''));
		$c->short_name = trim((string)Req::post('short_name', '')) ?: $this->slugify($c->name);
		$c->in_home = isset($_POST['in_home']) ? 1 : 0;
		$c->in_menu = isset($_POST['in_menu']) ? 1 : 0;
		$c->is_active = isset($_POST['is_active']) ? 1 : 0;

		try {
			$c->update();
			$_SESSION['updated'] = 'Categoría actualizada.';
		} catch (\PDOException $e) {
			$_SESSION['error'] = 'Error al actualizar categoría.';
		}
		Response::redirect($this->baseFolder . '/categories');
	}

	public function delete(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		try {
			CategoryData::delById($id);
			$_SESSION['deleted'] = 'Categoría eliminada.';
		} catch (\PDOException $e) {
			$_SESSION['error'] = 'No se puede eliminar: tiene productos asociados.';
		}
		Response::redirect($this->baseFolder . '/categories');
	}
}
