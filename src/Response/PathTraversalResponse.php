<?php

namespace Birke\ThumbnailCreator\Response;

class PathTraversalResponse extends FailureResponse {

	public function __construct( string $sourcePath ) {
		parent::__construct( "Path '$sourcePath' goes outside allowed directory" );
	}
	
}
