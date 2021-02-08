<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\SupportAdoption;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class SupportAdoptionController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {
		throw new \Exception('This page can not be accessed right now.'); // @todo
	}


	public function render(): void
	{
		$params = [
			'contactsPageLink' => $this->base->getLinkFor('kontakty'),
		];

		$this->latte->render(
			__DIR__ . '/SupportAdoptionController.latte',
			\array_merge($this->base->getLayoutVariables('supportadoption'), $params),
		);
	}

}
