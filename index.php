<?php declare(strict_types = 1);

use Grifart\GeocodingClient\Caching\CachedGeocodingService;
use Grifart\GeocodingClient\Caching\CacheManager;
use Grifart\GeocodingClient\MapyCz\Communicator;
use Grifart\GeocodingClient\MapyCz\Mapping\Mapper;
use Grifart\GeocodingClient\MapyCz\MapyCzGeocodingService;
use GuzzleHttp\Client as HttpClient;
use HnutiBrontosaurus\BisApiClient\Client;
use HnutiBrontosaurus\Theme\Configuration;
use HnutiBrontosaurus\Theme\EcomailClient;
use HnutiBrontosaurus\Theme\SentryLogger;
use HnutiBrontosaurus\Theme\UI\AboutStructure\GeocodingClientFacade;
use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use HnutiBrontosaurus\Theme\UI\ControllerFactory;
use HnutiBrontosaurus\Theme\UI\EventDetail\ApplicationFormFacade;
use HnutiBrontosaurus\Theme\UI\EventDetail\EmailSettings;
use HnutiBrontosaurus\Theme\UI\NotFound;
use Latte\Bridges\Tracy\BlueScreenPanel;
use Latte\Bridges\Tracy\LattePanel;
use Latte\Engine;
use Nette\Http\RequestFactory;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;
use Tracy\Debugger;


require_once __DIR__ . '/vendor/autoload.php';


// DI & bootstrap in one

(function (?\WP_Post $post) {
	// latte
	$cachePath = __DIR__ . '/temp/cache/latte';
	if ( ! \is_dir($cachePath)) {
		if ( ! @mkdir($cachePath, recursive: true)) {
			throw new \RuntimeException('Can not create cache path.');
		}
	}
	$latte = (new Engine())->setTempDirectory($cachePath);

	// tracy
	Debugger::$logDirectory = __DIR__ . '/log';
	Debugger::$strictMode = true;
	Debugger::enable();
	BlueScreenPanel::initialize();
	LattePanel::initialize($latte);

	// config
	$configuration = new Configuration([
		__DIR__ . '/config/config.neon',
		__DIR__ . '/config/config.local.neon',
	]);

	// app

	$bisApiClient = new Client(
		$configuration->get('bis:url'),
		$configuration->get('bis:username'),
		$configuration->get('bis:password'),
		new HttpClient(),
	);

	if ($configuration->get('mailer:smtp')) {
		// only no-authentication access is supported right now as we do not have authenticated SMTP servers now
		$options = [];
		$options['host'] = $configuration->get('mailer:host');

		try {
			$port = $configuration->get('mailer:port');
			$options['port'] = $port;

		} catch (Exception) {}

		$mailer = new SmtpMailer($options);
	} else {
		$mailer = new SendmailMailer();
	}

	try {
		$dsn = $configuration->get('sentry:dsn');

		if ($dsn === '') {
			$dsn = null;
		}

	} catch (\Exception) {
		$dsn = null;
	}
	$sentryLogger = SentryLogger::initialize($dsn);

	$controllerFactory = new ControllerFactory(
		$configuration->get('dateFormat:human'),
		$configuration->get('dateFormat:robot'),
		$configuration->get('recaptcha:siteKey'),
		$configuration->get('recaptcha:secretKey'),
		new ApplicationFormFacade(
			$bisApiClient,
			$mailer,
			EmailSettings::from(
				$configuration->get('mailer:from:address'),
				$configuration->get('mailer:from:name'),
			),
			$sentryLogger,
		),
		$bisApiClient,
		new BaseFactory($configuration->get('enableTracking')),
		new EcomailClient(
			$configuration->get('ecomail:brontowebContactsListId'),
			new Ecomail($configuration->get('ecomail:apiKey')),
		),
		$latte,
		new GeocodingClientFacade(
			new CachedGeocodingService(
				new CacheManager(__DIR__ . '/temp/geocoding-cache'),
				new MapyCzGeocodingService(
					new Communicator(),
					new Mapper(),
				),
			),
		),
		(new RequestFactory())->fromGlobals(),
		$sentryLogger,
	);

	try {
		$controller = $controllerFactory->create( // routing is contained inside
			$post,
			isset($_GET['preview']) && $_GET['preview'] === 'true',
		);
		$controller->render();

	} catch (NotFound) {
		$controllerFactory->render404();
	}
})(get_post());
