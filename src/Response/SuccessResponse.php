<?php

namespace Birke\ThumbnailCreator\Response;

class SuccessResponse {

	public function __construct( public readonly string $path ) {
	}
	
}
