<?php

use Symfony\Component\HttpFoundation\Request;
use Birke\ThumbnailCreator\ThumbnailController;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$controller = new ThumbnailController(
	sourceDir: '/pdfs',
	thumbnailDir: '/thumbnails',
	thumbnailPrefix: '/thumbnails/'
);
$response = $controller->index($request);

$response->send();


