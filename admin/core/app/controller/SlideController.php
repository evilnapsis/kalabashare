<?php

namespace App\Controller;

use SlideData;
use ViewEngine;
use Req;
use Response;

class SlideController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
	}

	public function index(): void {
		ViewEngine::render('slides/index.html.twig', [
			'slides' => SlideData::getAll(),
		]);
	}

	public function new(): void {
		ViewEngine::render('slides/new.html.twig');
	}

	private function uploadImage(string $fieldName, string $destFolder): string {
		if (empty($_FILES[$fieldName]['name'])) {
			return '';
		}
		$handle = new \Upload($_FILES[$fieldName]);
		if ($handle->uploaded) {
			$handle->file_new_name_body = uniqid('slide_');
			$handle->allowed = ['image/*'];
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
		$img = $this->uploadImage('image', ROOT . '/storage/slides/');
		if ($img === '') {
			$_SESSION['error'] = 'Debes seleccionar una imagen para el slide.';
			Response::redirect($this->baseFolder . '/slides/new');
		}

		$s = new SlideData();
		$s->title = trim((string)Req::post('title', ''));
		$s->link = trim((string)Req::post('link', ''));
		$s->position = max(1, (int)Req::post('position', 1));
		$s->is_public = isset($_POST['is_public']) ? 1 : 0;
		$s->image = $img;
		$s->add();

		$_SESSION['success'] = 'Slide agregado correctamente.';
		Response::redirect($this->baseFolder . '/slides');
	}

	public function edit(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$slide = SlideData::getById($id);
		if (!$slide) {
			$_SESSION['error'] = 'Slide no encontrado.';
			Response::redirect($this->baseFolder . '/slides');
		}
		ViewEngine::render('slides/edit.html.twig', ['slide' => $slide]);
	}

	public function update(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		$s = SlideData::getById($id);
		if (!$s) {
			$_SESSION['error'] = 'Slide no encontrado.';
			Response::redirect($this->baseFolder . '/slides');
		}

		$s->title = trim((string)Req::post('title', ''));
		$s->link = trim((string)Req::post('link', ''));
		$s->position = max(1, (int)Req::post('position', 1));
		$s->is_public = isset($_POST['is_public']) ? 1 : 0;

		$newImg = $this->uploadImage('image', ROOT . '/storage/slides/');
		if ($newImg !== '') {
			$s->image = $newImg;
		}
		$s->update();

		$_SESSION['updated'] = 'Slide actualizado.';
		Response::redirect($this->baseFolder . '/slides');
	}

	public function delete(array $vars): void {
		$id = (int)($vars['id'] ?? 0);
		SlideData::delById($id);
		$_SESSION['deleted'] = 'Slide eliminado.';
		Response::redirect($this->baseFolder . '/slides');
	}
}
