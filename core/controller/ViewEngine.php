<?php

class ViewEngine {
	private static ?\Twig\Environment $twig = null;

	public static function init(): \Twig\Environment {
		if (self::$twig === null) {
			$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../public');
			self::$twig = new \Twig\Environment($loader, [
				'cache' => false,
				'debug' => true,
			]);

			$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
			self::$twig->addGlobal('base_url', $baseUrl);
			self::$twig->addGlobal('csrf_token', Session::csrfToken());
			self::$twig->addFunction(new \Twig\TwigFunction('csrf_token', fn() => Session::csrfToken()));
			self::$twig->addFunction(new \Twig\TwigFunction('csrf_field', function () {
				return '<input type="hidden" name="csrf_token" value="' . Session::csrfToken() . '">';
			}, ['is_safe' => ['html']]));
		}
		return self::$twig;
	}

	public static function render(string $template, array $data = []): void {
		$twig = self::init();
		$twig->addGlobal('site_config', ConfigurationData::getMap());
		$twig->addGlobal('menu_categories', CategoryData::getMenuCategories());
		$twig->addGlobal('curr_client', isset($_SESSION['client_id']) ? ClientData::getById((int)$_SESSION['client_id']) : null);

		foreach (['success', 'updated', 'deleted', 'error'] as $key) {
			$twig->addGlobal('flash_' . $key, $_SESSION[$key] ?? null);
			unset($_SESSION[$key]);
		}

		echo $twig->render($template, $data);
	}
}
