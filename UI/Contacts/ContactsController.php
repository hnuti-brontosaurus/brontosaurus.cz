<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Contacts;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class ContactsController implements Controller
{
	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$params = [
			'aboutStructurePageLink' => '', // todo
			'imagesPath' => get_template_directory_uri() . '/UI/Contacts/assets/dist/images',
		];

		$this->latte->render(
			__DIR__ . '/ContactsController.latte',
			\array_merge($this->base->getLayoutVariables('contacts'), $params),
		);
	}

}
