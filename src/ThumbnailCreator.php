<?php
declare(strict_types=1);

namespace Birke\ThumbnailCreator;

use Birke\ThumbnailCreator\Response\FailureResponse;
use Birke\ThumbnailCreator\Response\PathTraversalResponse;
use Birke\ThumbnailCreator\Response\SourceNotFoundResponse;
use Birke\ThumbnailCreator\Response\SuccessResponse;
use Birke\ThumbnailCreator\Response\UnsupportedOutputTypeResponse;

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

	public function createThumbnail(string $path): SuccessResponse|FailureResponse {
		$pathInfo = pathinfo($path);
			
		if ($pathInfo['extension'] !== 'jpg' && $pathInfo['extension'] !== 'jpeg') {
			return new UnsupportedOutputTypeResponse( $pathInfo['extension'], 'jpg' );
		}

		$subPath = str_replace($this->thumbnailPrefix, '', $pathInfo['dirname']);

		$sourceName = $this->joinPaths( $this->sourceDir, $subPath, $pathInfo['filename'] );

		if ( !file_exists($sourceName)) {
			return new SourceNotFoundResponse($sourceName);
		}
		if ( $this->pathHasTraversal($sourceName)) {
			return new PathTraversalResponse($sourceName);
		}
	
		$destinationName = $this->joinPaths( $this->thumbnailDir, $subPath, $pathInfo['basename'] );
		$this->createOutputDirectoryIfNeeded($destinationName);

		// TODO limit width to 400px (to allow for hi-dpi display of 200px thumbnails)

		$command = "convert -density 72 \"{$sourceName}[0]\" -colorspace sRGB \"{$destinationName}\"";

		// TODO capture output and check for return code, logging if command is not successful
		exec($command);
		return new SuccessResponse($destinationName);
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