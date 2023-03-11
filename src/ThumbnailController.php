<?php
declare(strict_types=1);

namespace Birke\ThumbnailCreator;

use Birke\ThumbnailCreator\Response\FailureResponse;
use Birke\ThumbnailCreator\Response\FileExistsResponse;
use Birke\ThumbnailCreator\Response\PathTraversalResponse;
use Birke\ThumbnailCreator\Response\SourceNotFoundResponse;
use Birke\ThumbnailCreator\Response\SuccessResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThumbnailController {
	public function __construct(
		private readonly ThumbnailCreator $thumbnailCreator,
		private readonly string $urlPrefix = ''
	) {}

	public function index(Request $request): Response {
		$path = urldecode($request->getPathInfo());

		if ($this->urlPrefix && str_starts_with($path, $this->urlPrefix)) {
			$path = substr($path, strlen($this->urlPrefix));
		}

		$response = $this->thumbnailCreator->createThumbnail($path);

		if ( !( $response instanceof SuccessResponse ) ) {
			return new Response($response->message, $this->getResponseCode($response), ['content-type' => 'text/plain'] );
		}

		if ( $response instanceof FileExistsResponse ) {
			error_log("Got request for existing file, check your web server configuration.");
		}

		BinaryFileResponse::trustXSendfileTypeHeader();
		$httpResponse = new BinaryFileResponse($response->path);
		$httpResponse->prepare($request);
		return $httpResponse;
	}

	private function getResponseCode(FailureResponse $response): int {
		return match(get_class($response)) {
			PathTraversalResponse::class => Response::HTTP_FORBIDDEN,
			SourceNotFoundResponse::class => Response::HTTP_NOT_FOUND,
			default => Response::HTTP_BAD_REQUEST		
		};
	}
}
