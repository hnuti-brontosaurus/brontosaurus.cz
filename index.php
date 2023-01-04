<?php declare(strict_types = 1);

use Grifart\GeocodingClient\Caching\CachedGeocodingService;
use Grifart\GeocodingClient\Caching\CacheManager;
use Grifart\GeocodingClient\MapyCz\Communicator;
use Grifart\GeocodingClient\MapyCz\Mapping\Mapper;
use Grifart\GeocodingClient\MapyCz\MapyCzGeocodingService;
use GuzzleHttp\Client as HttpClient;
use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\BisClientFactory;
use HnutiBrontosaurus\LegacyBisApiClient\Client;
use HnutiBrontosaurus\Theme\Configuration;
use HnutiBrontosaurus\Theme\CoordinatesResolver\CoordinatesResolver;
use HnutiBrontosaurus\Theme\NotFound;
use HnutiBrontosaurus\Theme\SentryLogger;
use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use HnutiBrontosaurus\Theme\UI\ControllerFactory;
use HnutiBrontosaurus\Theme\UI\EventDetail\ApplicationFormFacade;
use HnutiBrontosaurus\Theme\UI\EventDetail\EmailSettings;
use Latte\Bridges\Tracy\BlueScreenPanel;
use Latte\Bridges\Tracy\LattePanel;
use Latte\Engine;
use Nette\Http\RequestFactory;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;
use Tracy\Debugger;


require_once __DIR__ . '/bootstrap.php';


// DI & bootstrap in one

function hb_getLatte(): Engine
{
	$cachePath = __DIR__ . '/temp/cache/latte';
	if ( ! \is_dir($cachePath)) {
		if ( ! @mkdir($cachePath, recursive: true)) {
			throw new \RuntimeException('Can not create cache path.');
		}
	}

	$latte = new Engine();
	$latte->setTempDirectory($cachePath);

	return $latte;
}

function hb_getCoordinatesResolver(): CoordinatesResolver
{
	return new CoordinatesResolver(
		new CachedGeocodingService(
			new CacheManager(__DIR__ . '/temp/geocoding-cache'),
			new MapyCzGeocodingService(
				new Communicator(),
				new Mapper(),
			),
		),
	);
}

function hb_getBisApiClient(Configuration $configuration): BisClient
{
	$factory = new BisClientFactory($configuration->get('bis:url'));
	return $factory->create();
}

function hb_getLegacyBisApiClient(Configuration $configuration): Client
{
	return new Client(
		$configuration->get('bis:legacy:url'),
		$configuration->get('bis:legacy:username'),
		$configuration->get('bis:legacy:password'),
		new HttpClient(),
	);
}

function hb_getDateFormatForHuman(Configuration $configuration): string
{
	return $configuration->get('dateFormat:human');
}

function hb_getDateFormatForRobot(Configuration $configuration): string
{
	return $configuration->get('dateFormat:robot');
}

(function (?\WP_Post $post) {
	// latte
	$latte = hb_getLatte();

	// tracy
	Debugger::$logDirectory = __DIR__ . '/log';
	Debugger::$strictMode = true;
	Debugger::enable();
	BlueScreenPanel::initialize();
	LattePanel::initialize($latte);

	// config
	$configuration = hb_getConfiguration();

	// app

	$bisApiClient = hb_getLegacyBisApiClient($configuration);

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


	// use template part if available
	if ($post !== null) {
		ob_start();
		set_query_var('hb_enableTracking', $configuration->get('enableTracking')); // temporary pass setting to template (better solution is to use WP database to store these things)
		if ($post->post_content !== '') {
			// todo use the_post() instead, but it did not work in current flow (with the controller things probably)
			echo '<h1>'.$post->post_title.'</h1>';
			echo '<div class="content__dynamic">' . apply_filters('the_content', $post->post_content) . '</div>';
			$result = null;
		} else {
			$result = get_template_part('template-parts/content/content', $post->post_name);
		}
		$content = ob_get_contents();
		ob_end_clean();
		$success = $result !== false; // get_template_part() doesn't return true in case of success
		if ($success) {
			get_header();
			echo $content;
			get_footer();
			return;
		}
	}


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
		$latte,
		hb_getCoordinatesResolver(),
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
