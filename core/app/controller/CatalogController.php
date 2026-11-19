<?php

namespace App\Controller;

use PostData;
use PostViewData;
use CategoryData;
use ViewEngine;
use Req;
use Response;

class CatalogController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		ViewEngine::render('catalog/index.html.twig', [
			'page_title' => 'Explorar Archivos',
			'posts' => PostData::getPublics(),
			'categories' => CategoryData::getPublics(),
			'active_slug' => '',
		]);
	}

	public function byCategory(array $vars): void {
		$slug = trim((string)($vars['slug'] ?? ''));
		$cat = CategoryData::getBySlug($slug);
		if (!$cat) {
			http_response_code(404);
			ViewEngine::render('404.html.twig');
			return;
		}
		ViewEngine::render('catalog/index.html.twig', [
			'page_title' => $cat->name,
			'posts' => PostData::getPublicsByCategory((int)$cat->id),
			'categories' => CategoryData::getPublics(),
			'active_slug' => $slug,
		]);
	}

	public function search(): void {
		$q = trim((string)Req::get('q', ''));
		$posts = $q === '' ? PostData::getPublics() : PostData::searchPublic($q);
		ViewEngine::render('catalog/index.html.twig', [
			'page_title' => $q !== '' ? 'Búsqueda: ' . $q : 'Explorar Archivos',
			'posts' => $posts,
			'categories' => CategoryData::getPublics(),
			'active_slug' => '',
			'search_query' => $q,
		]);
	}

	public function detail(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$post = PostData::getById($id);
		if (!$post || !$post->is_public) {
			http_response_code(404);
			ViewEngine::render('404.html.twig');
			return;
		}

		PostViewData::record($id, $_SERVER['REMOTE_ADDR'] ?? null);

		ViewEngine::render('catalog/detail.html.twig', [
			'post' => $post,
			'categories' => CategoryData::getPublics(),
		]);
	}

	public function download(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$post = PostData::getById($id);
		if (!$post || !$post->is_public) {
			http_response_code(404);
			ViewEngine::render('404.html.twig');
			return;
		}

		PostData::incrementDownloads($id);

		if (!empty($post->file)) {
			$filePath = ROOT . '/admin/storage/files/' . basename($post->file);
			if (file_exists($filePath)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
				header('Content-Length: ' . filesize($filePath));
				readfile($filePath);
				exit;
			}
		}

		if (!empty($post->link)) {
			Response::redirect($post->link);
		}

		$_SESSION['error'] = 'El archivo no está disponible para descarga.';
		Response::redirect($this->baseFolder . '/post/' . $id);
	}
}
