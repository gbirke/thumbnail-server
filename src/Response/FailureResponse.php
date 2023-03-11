<?php

namespace Birke\ThumbnailCreator\Response;

abstract class FailureResponse {

	public function __construct(public readonly string $message){}
	
}
