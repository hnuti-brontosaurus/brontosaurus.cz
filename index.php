<?php declare(strict_types = 1);

use Grifart\GeocodingClient\Providers\Cache\CacheManager;
use Grifart\GeocodingClient\Providers\Cache\CacheProvider;
use Grifart\GeocodingClient\Providers\MapyCz\MapyCzProvider;
use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\BisClientFactory;
use HnutiBrontosaurus\Theme\ApplicationUrlTemplate;
use HnutiBrontosaurus\Theme\Configuration;
use HnutiBrontosaurus\Theme\CoordinatesResolver\CoordinatesResolver;
use HnutiBrontosaurus\Theme\NotFound;
use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use HnutiBrontosaurus\Theme\UI\ControllerFactory;
use Latte\Bridges\Tracy\BlueScreenPanel;
use Latte\Bridges\Tracy\LattePanel;
use Latte\Engine;
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
		new CacheProvider(
			new CacheManager(__DIR__ . '/temp/geocoding-cache'),
			new MapyCzProvider(),
		),
	);
}

function hb_getBisApiClient(Configuration $configuration): BisClient
{
	return (new BisClientFactory(
		$configuration->get('bis:url'),
	))->create();
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

	$bisApiClient = hb_getBisApiClient($configuration);

	if ($configuration->has('sentry:dsn') && ($dsn = $configuration->get('sentry:dsn')) !== '') {
		\Sentry\init(['dsn' => $dsn]);
	}


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
		ApplicationUrlTemplate::from($configuration->get('bis:applicationUrlTemplate')),
		$bisApiClient,
		new BaseFactory($configuration->get('enableTracking')),
		$latte,
		hb_getCoordinatesResolver(),
	);

	try {
		$controller = $controllerFactory->create( // routing is contained inside
			$post,
			isset($_GET['preview']) && $_GET['preview'] === 'true',
		);
		$controller->render();

	} catch (NotFound) {
		$controllerFactory->create404()
			->render();
	}
})(get_post());
