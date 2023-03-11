<?php

namespace Birke\ThumbnailCreator\Response;

class UnsupportedOutputTypeResponse extends FailureResponse {

	public function __construct( string $fileType, string $supported ) {
		parent::__construct( "Invalid thumbnail format '{$fileType}', must be one of the following: $supported" );
	}
	
}
