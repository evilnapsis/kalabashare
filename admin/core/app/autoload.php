<?php

function kat2_admin_autoload($classname) {
	if (Model::exists($classname)) {
		include Model::getFullpath($classname);
		return;
	}

	if (strpos($classname, 'App\\') === 0) {
		$parts = explode('\\', $classname);
		if (count($parts) >= 3) {
			$subfolder = strtolower($parts[1]);
			$file = $parts[2];
			$fullpath = __DIR__ . "/" . $subfolder . "/" . $file . ".php";
			if (file_exists($fullpath)) {
				include $fullpath;
			}
		}
	}
}

spl_autoload_register("kat2_admin_autoload", true, true);
