<?php

use Symfony\Component\HttpFoundation\Request;
use Birke\ThumbnailCreator\ThumbnailController;
use Birke\ThumbnailCreator\ThumbnailCreator;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$controller = new ThumbnailController(
	new ThumbnailCreator(
		sourceDir: '/pdfs',
		thumbnailDir: '/thumbnails',
		thumbnailPrefix: '/thumbnails/'
	)
);
$response = $controller->index($request);

$response->send();
