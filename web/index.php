<?php

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

class ThumbnailCreator {
	public function __construct(
		private readonly string $sourceDir,
		private readonly string $thumbnailDir,
		private readonly string $thumbnailPrefix
	) {}

	private function joinPaths(string ...$parts): string {
		if (count($parts) === 0) return '';

		$prefix = ($parts[0] === DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR : '';
		$processed = array_filter(array_map(function ($part) {
			return rtrim($part, DIRECTORY_SEPARATOR);
		}, $parts), function ($part) {
			return !empty($part);
		});

		return $prefix . implode(DIRECTORY_SEPARATOR, $processed);
	}

	public function createThumbnail(string $path): Response {
		$pathInfo = pathinfo($path);
			
		if ($pathInfo['extension'] !== 'jpg' && $pathInfo['extension'] !== 'jpeg') {
			return new Response("Invalid thumbnail format '{$pathInfo['extension']}', must be JPG", Response::HTTP_BAD_REQUEST, ['content-type' => 'text/plain']);
		}

		// TODO check if source is a PDF, otherwise generate generic mime type image (if it's a JPG, pass it through)

		$subPath = str_replace($this->thumbnailPrefix, '', $pathInfo['dirname']);

		$sourceName = $this->joinPaths( $this->sourceDir, $subPath, $pathInfo['filename'] );

		if ( !file_exists($sourceName)) {
			return new Response("Path $sourceName does not exist", Response::HTTP_NOT_FOUND, ['content-type' => 'text/plain']);
		}
		if ( $this->pathHasTraversal($sourceName)) {
			return new Response("Path $sourceName goes outside allowed directory", Response::HTTP_FORBIDDEN, ['content-type' => 'text/plain']);
		}
	
		$destinationName = $this->joinPaths( $this->thumbnailDir, $subPath, $pathInfo['basename'] );
		$this->createOutputDirectoryIfNeeded($destinationName);

		// TODO limit width to 400px (to allow for hi-dpi display of 200px thumbnails)

		$command = "convert -density 72 \"{$sourceName}[0]\" -colorspace sRGB \"{$destinationName}\"";

		// TODO capture output and check for return code, logging if command is not successful
		exec($command);
		return new RedirectResponse($path . "?thumbnail_created=1");
	}

	private function pathHasTraversal(string $sourceName): bool {
		$finalPath = realpath($sourceName);
		return $finalPath === false || !str_starts_with($finalPath, $this->sourceDir);
	}

	private function createOutputDirectoryIfNeeded(string $outputFile): void {
		$destinationPath = dirname($outputFile);
		if (!file_exists($destinationPath)) {
			mkdir($destinationPath, 0777, true );
		}
	}
}


$request = Request::createFromGlobals();

// Prevent endless loop
if ($request->query->has('thumbnail_created')) {
	$response = new Response('Error - Can\'t create thumbnails twice', Response::HTTP_BAD_REQUEST, ['content-type' => 'text/plain'] );
	$response->send();
	exit;
}
$path = urldecode($request->getPathInfo());

$thumbnailer = new ThumbnailCreator(
	sourceDir: '/pdfs',
	thumbnailDir: '/thumbnails',
	thumbnailPrefix: '/thumbnails/'
);
$response = $thumbnailer->createThumbnail($path);

$response->send();


