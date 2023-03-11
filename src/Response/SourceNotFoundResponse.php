<?php

namespace Birke\ThumbnailCreator\Response;

class SourceNotFoundResponse extends FailureResponse {

	public function __construct( string $sourcePath ) {
		parent::__construct( "Path '$sourcePath' does not exist" );
	}
	
}
