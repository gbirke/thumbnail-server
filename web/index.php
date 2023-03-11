<?php

use Birke\ThumbnailCreator\AppFactory;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
	(new Dotenv())->load(__DIR__ . '/../.env');
}

$request = Request::createFromGlobals();

$controller = AppFactory::getController();

$response = $controller->index($request);

$response->send();
