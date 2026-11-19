<?php
/**
 * Katalog 2 Admin - Front Controller (FastRoute + Twig + CSRF + RBAC)
 */

define("ROOT", __DIR__);

$debug = true;
if ($debug) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require_once __DIR__ . "/core/autoload.php";
require_once __DIR__ . "/core/app/autoload.php";

ob_start();
session_start();

use App\Service\AuthService;

$routes = require __DIR__ . '/core/app/routes.php';
$dispatcher = FastRoute\simpleDispatcher($routes);

$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
if (!empty($baseFolder) && strpos($uri, $baseFolder) === 0) {
	$uri = substr($uri, strlen($baseFolder));
}
if (empty($uri)) {
	$uri = '/';
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
	case FastRoute\Dispatcher::NOT_FOUND:
		http_response_code(404);
		ViewEngine::render('404.html.twig');
		break;

	case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
		http_response_code(405);
		echo "<h1>405 - Método no permitido</h1>";
		break;

	case FastRoute\Dispatcher::FOUND:
		if ($httpMethod === 'POST') {
			$token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
			if (!Session::validateCsrf($token)) {
				http_response_code(403);
				echo "<h1>403 - Token CSRF inválido</h1><p>Recarga el formulario e intenta de nuevo.</p>";
				exit;
			}
		}

		$protectedControllers = [
			App\Controller\HomeController::class,
			App\Controller\PostController::class,
			App\Controller\CategoryController::class,
			App\Controller\ClientController::class,
			App\Controller\SlideController::class,
			App\Controller\UserController::class,
			App\Controller\ConfigurationController::class,
			App\Controller\ProfileController::class,
		];

		$handler = $routeInfo[1];
		$vars = $routeInfo[2];

		if (in_array($handler[0], $protectedControllers, true) && !AuthService::check()) {
			header('Location: ' . ($baseFolder ?: '') . '/login');
			exit;
		}

		[$controllerClass, $method] = $handler;

		if (AuthService::check()) {
			$authUser = AuthService::user();
			$userRole = $authUser ? $authUser->role : ($_SESSION['user_role'] ?? 'editor');
			$_SESSION['user_role'] = $userRole;

			if ($userRole !== 'admin') {
				$forbiddenControllers = [
					App\Controller\UserController::class,
					App\Controller\ConfigurationController::class,
				];
				if (in_array($controllerClass, $forbiddenControllers, true)) {
					$_SESSION['error'] = 'No tienes permiso para acceder a este módulo.';
					header('Location: ' . ($baseFolder ?: '') . '/home');
					exit;
				}
			}
		}

		$controller = new $controllerClass();
		$controller->$method($vars);
		break;
}
