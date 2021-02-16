<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Base;


final class BaseFactory
{
	public function __construct(
		private bool $enableTracking,
	) {}

	public function create(?\WP_Post $currentPost): Base
	{
		return new Base($this->enableTracking, $currentPost);
	}

}
