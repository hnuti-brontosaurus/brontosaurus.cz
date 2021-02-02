<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Error\ErrorController;
use Latte\Engine;


final class ControllerFactory
{
	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}

	/*
	 * This is basically router.
	 */
	public function create(\WP_Post $post): Controller
	{
		if ($post->post_type !== 'page') {
			return new ErrorController($this->base, $this->latte);
		}

		return match ($post->post_name) {
			default => new ErrorController($this->base, $this->latte),
		};
	}

}
