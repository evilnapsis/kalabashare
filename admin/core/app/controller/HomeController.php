<?php

namespace App\Controller;

use PostData;
use CategoryData;
use ClientData;
use PostViewData;
use ViewEngine;

class HomeController {
	public function index(): void {
		$posts = PostData::getAll();
		$categories = CategoryData::getAll();
		$clients = ClientData::getAll();
		$totalViews = PostViewData::countTotal();
		$topViewed = PostViewData::getTopViewed(5);
		$chartRaw = PostViewData::getLast30DaysChart();

		$mapByDate = [];
		foreach ($chartRaw as $row) {
			$mapByDate[$row->view_date] = (int)$row->total_views;
		}
		$chartLabels = [];
		$chartViews = [];
		for ($i = 29; $i >= 0; $i--) {
			$d = date('Y-m-d', strtotime("-{$i} days"));
			$chartLabels[] = date('d/m', strtotime($d));
			$chartViews[] = $mapByDate[$d] ?? 0;
		}

		ViewEngine::render('home/index.html.twig', [
			'posts_count' => count($posts),
			'categories_count' => count($categories),
			'clients_count' => count($clients),
			'total_views' => $totalViews,
			'top_viewed' => $topViewed,
			'chart_labels' => json_encode($chartLabels),
			'chart_views' => json_encode($chartViews),
		]);
	}
}
