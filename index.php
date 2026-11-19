<?php
/**
 * Kalabashare 2 - Front Controller Público (FastRoute + Twig + CSRF)
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
			$token = $_POST['csrf_token'] ?? null;
			if (!Session::validateCsrf($token)) {
				http_response_code(403);
				echo "<h1>403 - Token CSRF inválido</h1><p>Recarga el formulario e intenta de nuevo.</p>";
				exit;
			}
		}

		$handler = $routeInfo[1];
		$vars = $routeInfo[2];

		if ($handler[0] === App\Controller\ClientPortalController::class && empty($_SESSION['client_id'])) {
			$_SESSION['error'] = 'Inicia sesión para gestionar tus archivos.';
			header('Location: ' . ($baseFolder ?: '') . '/login');
			exit;
		}

		[$controllerClass, $method] = $handler;
		$controller = new $controllerClass();
		$controller->$method($vars);
		break;
}
