<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\FirstTime;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\Voluntary\VoluntaryController;
use Latte\Engine;


final class FirstTimeController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$params = [
			'filtersKey' => VoluntaryController::FILTER_KEY,
		];

		$this->latte->render(
			__DIR__ . '/FirstTimeController.latte',
			\array_merge($this->base->getLayoutVariables('firsttime'), $params),
		);
	}

}
