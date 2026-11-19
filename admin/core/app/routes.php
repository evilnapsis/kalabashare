<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
	// Autenticación
	$r->addRoute('GET', '/', [App\Controller\AuthController::class, 'loginForm']);
	$r->addRoute('GET', '/login', [App\Controller\AuthController::class, 'loginForm']);
	$r->addRoute('POST', '/login', [App\Controller\AuthController::class, 'login']);
	$r->addRoute(['GET', 'POST'], '/logout', [App\Controller\AuthController::class, 'logout']);

	// Dashboard
	$r->addRoute('GET', '/home', [App\Controller\HomeController::class, 'index']);

	// Archivos / Publicaciones
	$r->addRoute('GET', '/posts', [App\Controller\PostController::class, 'index']);
	$r->addRoute('GET', '/posts/new', [App\Controller\PostController::class, 'new']);
	$r->addRoute('POST', '/posts/create', [App\Controller\PostController::class, 'create']);
	$r->addRoute('GET', '/posts/{id:\d+}/edit', [App\Controller\PostController::class, 'edit']);
	$r->addRoute('POST', '/posts/{id:\d+}/update', [App\Controller\PostController::class, 'update']);
	$r->addRoute('POST', '/posts/{id:\d+}/delete', [App\Controller\PostController::class, 'delete']);

	// Categorías
	$r->addRoute('GET', '/categories', [App\Controller\CategoryController::class, 'index']);
	$r->addRoute('GET', '/categories/new', [App\Controller\CategoryController::class, 'new']);
	$r->addRoute('POST', '/categories/create', [App\Controller\CategoryController::class, 'create']);
	$r->addRoute('GET', '/categories/{id:\d+}/edit', [App\Controller\CategoryController::class, 'edit']);
	$r->addRoute('POST', '/categories/{id:\d+}/update', [App\Controller\CategoryController::class, 'update']);
	$r->addRoute('POST', '/categories/{id:\d+}/delete', [App\Controller\CategoryController::class, 'delete']);

	// Clientes / Usuarios Registrados
	$r->addRoute('GET', '/clients', [App\Controller\ClientController::class, 'index']);
	$r->addRoute('GET', '/clients/new', [App\Controller\ClientController::class, 'new']);
	$r->addRoute('POST', '/clients/create', [App\Controller\ClientController::class, 'create']);
	$r->addRoute('GET', '/clients/{id:\d+}/edit', [App\Controller\ClientController::class, 'edit']);
	$r->addRoute('POST', '/clients/{id:\d+}/update', [App\Controller\ClientController::class, 'update']);
	$r->addRoute('POST', '/clients/{id:\d+}/delete', [App\Controller\ClientController::class, 'delete']);

	// Sliders
	$r->addRoute('GET', '/slides', [App\Controller\SlideController::class, 'index']);
	$r->addRoute('GET', '/slides/new', [App\Controller\SlideController::class, 'new']);
	$r->addRoute('POST', '/slides/create', [App\Controller\SlideController::class, 'create']);
	$r->addRoute('GET', '/slides/{id:\d+}/edit', [App\Controller\SlideController::class, 'edit']);
	$r->addRoute('POST', '/slides/{id:\d+}/update', [App\Controller\SlideController::class, 'update']);
	$r->addRoute('POST', '/slides/{id:\d+}/delete', [App\Controller\SlideController::class, 'delete']);

	// Usuarios (Solo Admin)
	$r->addRoute('GET', '/users', [App\Controller\UserController::class, 'index']);
	$r->addRoute('GET', '/users/new', [App\Controller\UserController::class, 'new']);
	$r->addRoute('POST', '/users/create', [App\Controller\UserController::class, 'create']);
	$r->addRoute('GET', '/users/{id:\d+}/edit', [App\Controller\UserController::class, 'edit']);
	$r->addRoute('POST', '/users/{id:\d+}/update', [App\Controller\UserController::class, 'update']);
	$r->addRoute('POST', '/users/{id:\d+}/delete', [App\Controller\UserController::class, 'delete']);

	// Configuración (Solo Admin)
	$r->addRoute('GET', '/settings', [App\Controller\ConfigurationController::class, 'index']);
	$r->addRoute('POST', '/settings/update', [App\Controller\ConfigurationController::class, 'update']);

	// Perfil
	$r->addRoute('GET', '/profile', [App\Controller\ProfileController::class, 'index']);
	$r->addRoute('POST', '/profile/update', [App\Controller\ProfileController::class, 'update']);
};
