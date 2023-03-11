<?php

namespace Birke\ThumbnailCreator;

use Birke\ThumbnailCreator\ThumbnailController;
use Birke\ThumbnailCreator\ThumbnailCreator;

class AppFactory {

	public static function getController(): ThumbnailController {
		return new ThumbnailController(
			self::getThumbnailCreator(),
			self::getEnvironmentValue('URL_PREFIX')
		);
	}

	private static function getThumbnailCreator(): ThumbnailCreator {
		return new ThumbnailCreator(
			self::getEnvironmentValue('SOURCE_PATH'),
			self::getEnvironmentValue('THUMBNAIL_PATH')
		);
	}

	private static function getEnvironmentValue(string $name, string $default = null ): string {
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];	
		}
		if ( $default !== null ) {
			return $default;
		}
		throw new \RuntimeException("Environment variable $name not set.");
	}

}
