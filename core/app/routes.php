<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
	// Portal público de archivos
	$r->addRoute('GET', '/', [App\Controller\HomeController::class, 'index']);
	$r->addRoute('GET', '/posts', [App\Controller\CatalogController::class, 'index']);
	$r->addRoute('GET', '/category/{slug}', [App\Controller\CatalogController::class, 'byCategory']);
	$r->addRoute('GET', '/search', [App\Controller\CatalogController::class, 'search']);
	$r->addRoute('GET', '/post/{id:\d+}', [App\Controller\CatalogController::class, 'detail']);
	$r->addRoute('GET', '/post/{id:\d+}/download', [App\Controller\CatalogController::class, 'download']);
	$r->addRoute('GET', '/contact', [App\Controller\ContactController::class, 'index']);
	$r->addRoute('POST', '/contact', [App\Controller\ContactController::class, 'send']);

	// Autenticación de clientes
	$r->addRoute('GET', '/login', [App\Controller\ClientAuthController::class, 'loginForm']);
	$r->addRoute('POST', '/login', [App\Controller\ClientAuthController::class, 'login']);
	$r->addRoute('GET', '/register', [App\Controller\ClientAuthController::class, 'registerForm']);
	$r->addRoute('POST', '/register', [App\Controller\ClientAuthController::class, 'register']);
	$r->addRoute(['GET', 'POST'], '/logout', [App\Controller\ClientAuthController::class, 'logout']);

	// Portal del cliente (Mis Archivos)
	$r->addRoute('GET', '/client', [App\Controller\ClientPortalController::class, 'index']);
	$r->addRoute('GET', '/client/posts/new', [App\Controller\ClientPortalController::class, 'newPost']);
	$r->addRoute('POST', '/client/posts/create', [App\Controller\ClientPortalController::class, 'createPost']);
	$r->addRoute('GET', '/client/posts/{id:\d+}/edit', [App\Controller\ClientPortalController::class, 'editPost']);
	$r->addRoute('POST', '/client/posts/{id:\d+}/update', [App\Controller\ClientPortalController::class, 'updatePost']);
	$r->addRoute('POST', '/client/posts/{id:\d+}/delete', [App\Controller\ClientPortalController::class, 'deletePost']);
};
