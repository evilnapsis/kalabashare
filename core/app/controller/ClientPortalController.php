<?php

namespace App\Controller;

use PostData;
use CategoryData;
use ViewEngine;
use Req;
use Response;

class ClientPortalController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		$clientId = (int)($_SESSION['client_id'] ?? 0);
		ViewEngine::render('client/index.html.twig', [
			'posts' => PostData::getByClientId($clientId),
		]);
	}

	public function newPost(): void {
		ViewEngine::render('client/new_post.html.twig', [
			'categories' => CategoryData::getPublics(),
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

	public function createPost(): void {
		$clientId = (int)($_SESSION['client_id'] ?? 0);
		$name = trim((string)Req::post('name', ''));
		$categoryId = (int)Req::post('category_id', 1);

		if ($name === '' || $categoryId <= 0) {
			$_SESSION['error'] = 'El nombre y la categoría son obligatorios.';
			Response::redirect($this->baseFolder . '/client/posts/new');
		}

		$p = new PostData();
		$p->category_id = $categoryId;
		$p->client_id = $clientId;
		$p->name = $name;
		$p->short_name = $this->slugify($name);
		$p->description = trim((string)Req::post('description', ''));
		$p->offer_txt = trim((string)Req::post('offer_txt', ''));
		$p->link = trim((string)Req::post('link', ''));
		$p->is_public = isset($_POST['is_public']) ? 1 : 0;
		$p->is_featured = 0;
		$p->image = $this->uploadFile('image', ROOT . '/admin/storage/products/', 'img_', true);
		$p->file = $this->uploadFile('file', ROOT . '/admin/storage/files/', 'file_', false);
		$p->add();

		$_SESSION['success'] = 'Archivo publicado correctamente.';
		Response::redirect($this->baseFolder . '/client');
	}

	public function editPost(array $vars): void {
		$clientId = (int)($_SESSION['client_id'] ?? 0);
		$id = (int)($vars['id'] ?? 0);
		$post = PostData::getById($id);

		if (!$post || (int)$post->client_id !== $clientId) {
			$_SESSION['error'] = 'No tienes permiso para editar este archivo.';
			Response::redirect($this->baseFolder . '/client');
		}

		ViewEngine::render('client/edit_post.html.twig', [
			'post' => $post,
			'categories' => CategoryData::getPublics(),
		]);
	}

	public function updatePost(array $vars): void {
		$clientId = (int)($_SESSION['client_id'] ?? 0);
		$id = (int)($vars['id'] ?? 0);
		$p = PostData::getById($id);

		if (!$p || (int)$p->client_id !== $clientId) {
			$_SESSION['error'] = 'No tienes permiso para editar este archivo.';
			Response::redirect($this->baseFolder . '/client');
		}

		$p->category_id = (int)Req::post('category_id', $p->category_id);
		$p->name = trim((string)Req::post('name', ''));
		$p->short_name = $this->slugify($p->name);
		$p->description = trim((string)Req::post('description', ''));
		$p->offer_txt = trim((string)Req::post('offer_txt', ''));
		$p->link = trim((string)Req::post('link', ''));
		$p->is_public = isset($_POST['is_public']) ? 1 : 0;

		$newImg = $this->uploadFile('image', ROOT . '/admin/storage/products/', 'img_', true);
		if ($newImg !== '') {
			$p->image = $newImg;
		}
		$newFile = $this->uploadFile('file', ROOT . '/admin/storage/files/', 'file_', false);
		if ($newFile !== '') {
			$p->file = $newFile;
		}
		$p->update();

		$_SESSION['updated'] = 'Archivo actualizado.';
		Response::redirect($this->baseFolder . '/client');
	}

	public function deletePost(array $vars): void {
		$clientId = (int)($_SESSION['client_id'] ?? 0);
		$id = (int)($vars['id'] ?? 0);
		$p = PostData::getById($id);

		if (!$p || (int)$p->client_id !== $clientId) {
			$_SESSION['error'] = 'No tienes permiso para eliminar este archivo.';
			Response::redirect($this->baseFolder . '/client');
		}

		PostData::delById($id);
		$_SESSION['deleted'] = 'Archivo eliminado.';
		Response::redirect($this->baseFolder . '/client');
	}
}
