<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI;

use HnutiBrontosaurus\BisApiClient\Client;
use HnutiBrontosaurus\Theme\SentryLogger;
use HnutiBrontosaurus\Theme\UI\AboutCrossroad\AboutCrossroadController;
use HnutiBrontosaurus\Theme\UI\AboutHighlights\AboutHighlightsController;
use HnutiBrontosaurus\Theme\UI\AboutStructure\AboutStructureController;
use HnutiBrontosaurus\Theme\UI\AboutStructure\GeocodingClientFacade;
use HnutiBrontosaurus\Theme\UI\AboutSuccesses\AboutSuccessesController;
use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use HnutiBrontosaurus\Theme\UI\Contacts\ContactsController;
use HnutiBrontosaurus\Theme\UI\Courses\CoursesController;
use HnutiBrontosaurus\Theme\UI\English\EnglishController;
use HnutiBrontosaurus\Theme\UI\Error\ErrorController;
use HnutiBrontosaurus\Theme\UI\EventDetail\ApplicationFormFacade;
use HnutiBrontosaurus\Theme\UI\EventDetail\EventDetailController;
use HnutiBrontosaurus\Theme\UI\FirstTime\FirstTimeController;
use HnutiBrontosaurus\Theme\UI\ForChildren\ForChildrenController;
use HnutiBrontosaurus\Theme\UI\Future\FutureController;
use HnutiBrontosaurus\Theme\UI\HighSchools\HighSchoolsController;
use HnutiBrontosaurus\Theme\UI\Homepage\HomepageController;
use HnutiBrontosaurus\Theme\UI\Meetups\MeetupsController;
use HnutiBrontosaurus\Theme\UI\News\NewsController;
use HnutiBrontosaurus\Theme\UI\Partners\PartnersController;
use HnutiBrontosaurus\Theme\UI\Rentals\RentalsController;
use HnutiBrontosaurus\Theme\UI\SupportAdoption\SupportAdoptionController;
use HnutiBrontosaurus\Theme\UI\SupportOverview\SupportOverviewController;
use HnutiBrontosaurus\Theme\UI\Voluntary\VoluntaryController;
use Latte\Engine;
use Nette\Http\Request;


final class ControllerFactory
{
	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private string $recaptchaSiteKey,
		private string $recaptchaSecretKey,
		private ApplicationFormFacade $applicationFormFacade,
		private Client $bisApiClient,
		private BaseFactory $baseFactory,
		private Engine $latte,
		private GeocodingClientFacade $geocodingClientFacade,
		private Request $httpRequest,
		private SentryLogger $logger,
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
			AboutCrossroadController::PAGE_SLUG => new AboutCrossroadController($base, $this->latte),
			VoluntaryController::PAGE_SLUG => new VoluntaryController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			CoursesController::PAGE_SLUG => new CoursesController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			MeetupsController::PAGE_SLUG => new MeetupsController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			ForChildrenController::PAGE_SLUG => new ForChildrenController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			'podpor-nas' => new SupportOverviewController($base, $this->latte),
			AboutHighlightsController::PAGE_SLUG => new AboutHighlightsController($base, $this->latte),
			AboutSuccessesController::PAGE_SLUG => new AboutSuccessesController($base, $this->latte),
			'english' => new EnglishController($base, $this->latte),
			'hlavni-stranka' => new HomepageController($base, $this->latte),
			'jedu-poprve' => new FirstTimeController($base, $this->latte),
			'programy-pro-stredni-skoly' => new HighSchoolsController($base, $this->latte),
			'nasi-partneri' => new PartnersController($base, $this->latte),
//			'adopce-brontosaura' => new SupportAdoptionController($base, $this->latte), // disabled for now
			FutureController::PAGE_SLUG => new FutureController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte),
			AboutStructureController::PAGE_SLUG => new AboutStructureController($this->dateFormatHuman, $this->dateFormatRobot, $this->bisApiClient, $base, $this->latte, $this->geocodingClientFacade),
			EventDetailController::PAGE_SLUG => new EventDetailController($this->dateFormatHuman, $this->dateFormatRobot, $this->recaptchaSiteKey, $this->recaptchaSecretKey, $this->applicationFormFacade, $this->bisApiClient, $base, $this->latte, $this->httpRequest, $this->logger),
			'co-je-noveho' => new NewsController($this->dateFormatHuman, $this->dateFormatRobot, $base, $this->latte),
			default => new ErrorController($base, $this->latte),
		};
	}

}
