<?php

namespace App\Controller;

use SlideData;
use PostData;
use CategoryData;
use ViewEngine;

class HomeController {
	public function index(): void {
		ViewEngine::render('home/index.html.twig', [
			'slides' => SlideData::getPublics(),
			'featured_posts' => PostData::getFeatureds(12),
			'categories' => CategoryData::getPublics(),
		]);
	}
}
