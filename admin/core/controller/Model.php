<?php

class Model {
	public static function exists($modelname) {
		return file_exists(self::getFullpath($modelname));
	}

	public static function getFullpath($modelname) {
		return dirname(__DIR__) . "/app/model/" . $modelname . ".php";
	}
}
