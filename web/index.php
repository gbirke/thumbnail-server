<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Birke\ThumbnailCreator\ThumbnailController;

require_once __DIR__ . '/../vendor/autoload.php';


$request = Request::createFromGlobals();

// Prevent endless loop
if ($request->query->has('thumbnail_created')) {
	$response = new Response('Error - Can\'t create thumbnails twice', Response::HTTP_BAD_REQUEST, ['content-type' => 'text/plain'] );
	$response->send();
	exit;
}
$path = urldecode($request->getPathInfo());

$thumbnailer = new ThumbnailController(
	sourceDir: '/pdfs',
	thumbnailDir: '/thumbnails',
	thumbnailPrefix: '/thumbnails/'
);
$response = $thumbnailer->createThumbnail($path);

$response->send();


