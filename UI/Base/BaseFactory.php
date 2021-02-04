<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Base;


final class BaseFactory
{

	public function create(\WP_Post $currentPost): Base
	{
		return new Base($currentPost);
	}

}
