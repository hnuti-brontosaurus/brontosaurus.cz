<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI;

use HnutiBrontosaurus\BisApiClient\Client;
use HnutiBrontosaurus\Theme\UI\AboutCrossroad\AboutCrossroadController;
use HnutiBrontosaurus\Theme\UI\AboutHighlights\AboutHighlightsController;
use HnutiBrontosaurus\Theme\UI\AboutSuccesses\AboutSuccessesController;
use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use HnutiBrontosaurus\Theme\UI\Contacts\ContactsController;
use HnutiBrontosaurus\Theme\UI\Courses\CoursesController;
use HnutiBrontosaurus\Theme\UI\English\EnglishController;
use HnutiBrontosaurus\Theme\UI\Error\ErrorController;
use HnutiBrontosaurus\Theme\UI\ForChildren\ForChildrenController;
use HnutiBrontosaurus\Theme\UI\Homepage\HomepageController;
use HnutiBrontosaurus\Theme\UI\Meetups\MeetupsController;
use HnutiBrontosaurus\Theme\UI\Rentals\RentalsController;
use HnutiBrontosaurus\Theme\UI\SupportOverview\SupportOverviewController;
use HnutiBrontosaurus\Theme\UI\Voluntary\VoluntaryController;
use Latte\Engine;


final class ControllerFactory
{
	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private Client $bisApiClient,
		private BaseFactory $baseFactory,
		private Engine $latte,
	) {
		Utils::registerFormatPhoneNumberLatteFilter($this->latte);
		Utils::registerTypeByDayCountLatteFilter($this->latte);
	}

	/*
	 * This is basically router.
	 */
	public function create(?\WP_Post $post): Controller
	{
		$base = $this->baseFactory->create($post);

		if ($post === null || $post->post_type !== 'page') {
			return new ErrorController($base, $this->latte);
		}

		return match ($post->post_name) {
			'kontakty' => new ContactsController($base, $this->latte),
			'pronajmy' => new RentalsController($base, $this->latte),
			'o-brontosaurovi' => new AboutCrossroadController($base, $this->latte),
			'dobrovolnicke-akce' => new VoluntaryController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			'kurzy-a-prednasky' => new CoursesController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			'setkavani-a-kluby' => new MeetupsController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			'pro-deti' => new ForChildrenController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			'podpor-nas' => new SupportOverviewController($base, $this->latte),
			'jak-to-u-nas-funguje' => new AboutHighlightsController($base, $this->latte),
			'nase-uspechy' => new AboutSuccessesController($base, $this->latte),
			'english' => new EnglishController($base, $this->latte),
			'hlavni-stranka' => new HomepageController($base, $this->latte),
			default => new ErrorController($base, $this->latte),
		};
	}

}
