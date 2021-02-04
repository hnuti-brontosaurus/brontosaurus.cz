<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI;

use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use HnutiBrontosaurus\Theme\UI\Contacts\ContactsController;
use HnutiBrontosaurus\Theme\UI\Error\ErrorController;
use Latte\Engine;


final class ControllerFactory
{
	public function __construct(
		private BaseFactory $baseFactory,
		private Engine $latte,
	) {}

	/*
	 * This is basically router.
	 */
	public function create(\WP_Post $post): Controller
	{
		$base = $this->baseFactory->create($post);

		if ($post->post_type !== 'page') {
			return new ErrorController($base, $this->latte);
		}

		return match ($post->post_name) {
			'kontakty' => new ContactsController($base, $this->latte),
			default => new ErrorController($base, $this->latte),
		};
	}

}
