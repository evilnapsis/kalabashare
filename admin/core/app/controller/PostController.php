<?php

namespace App\Controller;

use PostData;
use CategoryData;
use ClientData;
use ViewEngine;
use Req;
use Response;

class PostController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		ViewEngine::render('posts/index.html.twig', [
			'posts' => PostData::getAll(),
		]);
	}

	public function new(): void {
		ViewEngine::render('posts/new.html.twig', [
			'categories' => CategoryData::getPublics(),
			'clients' => ClientData::getAll(),
		]);
	}

	private function slugify(string $text): string {
		$slug = strtolower(trim($text));
		$slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
		return trim((string)$slug, '-');
	}

	private function uploadFile(string $fieldName, string $destFolder, string $prefix, bool $imageOnly = false): string {
		if (empty($_FILES[$fieldName]['name'])) {
			return '';
		}
		$handle = new \Upload($_FILES[$fieldName]);
		if ($handle->uploaded) {
			$handle->file_new_name_body = uniqid($prefix);
			if ($imageOnly) {
				$handle->allowed = ['image/*'];
			}
			$handle->process($destFolder);
			if ($handle->processed) {
				$name = $handle->file_dst_name;
				$handle->clean();
				return $name;
			}
		}
		return '';
	}

	public function create(): void {
		$name = trim((string)Req::post('name', ''));
		$categoryId = (int)Req::post('category_id', 1);
		if ($name === '' || $categoryId <= 0) {
			$_SESSION['error'] = 'El nombre y la categoría son obligatorios.';
			Response::redirect($this->baseFolder . '/posts/new');
		}

		$p = new PostData();
		$p->category_id = $categoryId;
		$p->client_id = (int)Req::post('client_id', 0) ?: null;
		$p->code = trim((string)Req::post('code', ''));
		$p->name = $name;
		$p->short_name = trim((string)Req::post('short_name', '')) ?: $this->slugify($name);
		$p->description = trim((string)Req::post('description', ''));
		$p->offer_txt = trim((string)Req::post('offer_txt', ''));
		$p->link = trim((string)Req::post('link', ''));
		$p->is_featured = isset($_POST['is_featured']) ? 1 : 0;
		$p->is_public = isset($_POST['is_public']) ? 1 : 0;
		$p->image = $this->uploadFile('image', ROOT . '/storage/products/', 'img_', true);
		$p->file = $this->uploadFile('file', ROOT . '/storage/files/', 'file_', false);
		$p->add();

		$_SESSION['success'] = 'Archivo publicado correctamente.';
		Response::redirect($this->baseFolder . '/posts');
	}

	public function edit(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$post = PostData::getById($id);
		if (!$post) {
			$_SESSION['error'] = 'Archivo no encontrado.';
			Response::redirect($this->baseFolder . '/posts');
		}
		ViewEngine::render('posts/edit.html.twig', [
			'post' => $post,
			'categories' => CategoryData::getPublics(),
			'clients' => ClientData::getAll(),
		]);
	}

	public function update(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$p = PostData::getById($id);
		if (!$p) {
			$_SESSION['error'] = 'Archivo no encontrado.';
			Response::redirect($this->baseFolder . '/posts');
		}

		$p->category_id = (int)Req::post('category_id', $p->category_id);
		$p->client_id = (int)Req::post('client_id', 0) ?: null;
		$p->code = trim((string)Req::post('code', ''));
		$p->name = trim((string)Req::post('name', ''));
		$p->short_name = trim((string)Req::post('short_name', '')) ?: $this->slugify($p->name);
		$p->description = trim((string)Req::post('description', ''));
		$p->offer_txt = trim((string)Req::post('offer_txt', ''));
		$p->link = trim((string)Req::post('link', ''));
		$p->is_featured = isset($_POST['is_featured']) ? 1 : 0;
		$p->is_public = isset($_POST['is_public']) ? 1 : 0;

		$newImage = $this->uploadFile('image', ROOT . '/storage/products/', 'img_', true);
		if ($newImage !== '') {
			$p->image = $newImage;
		}
		$newFile = $this->uploadFile('file', ROOT . '/storage/files/', 'file_', false);
		if ($newFile !== '') {
			$p->file = $newFile;
		}
		$p->update();

		$_SESSION['updated'] = 'Archivo actualizado correctamente.';
		Response::redirect($this->baseFolder . '/posts');
	}

	public function delete(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		PostData::delById($id);
		$_SESSION['deleted'] = 'Archivo eliminado.';
		Response::redirect($this->baseFolder . '/posts');
	}
}
