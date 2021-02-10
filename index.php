<?php declare(strict_types = 1);

use Grifart\GeocodingClient\Caching\CachedGeocodingService;
use Grifart\GeocodingClient\Caching\CacheManager;
use Grifart\GeocodingClient\MapyCz\Communicator;
use Grifart\GeocodingClient\MapyCz\Mapping\Mapper;
use Grifart\GeocodingClient\MapyCz\MapyCzGeocodingService;
use GuzzleHttp\Client as HttpClient;
use HnutiBrontosaurus\BisApiClient\Client;
use HnutiBrontosaurus\Theme\Configuration;
use HnutiBrontosaurus\Theme\UI\AboutStructure\GeocodingClientFacade;
use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use HnutiBrontosaurus\Theme\UI\ControllerFactory;
use Latte\Bridges\Tracy\BlueScreenPanel;
use Latte\Bridges\Tracy\LattePanel;
use Latte\Engine;
use Tracy\Debugger;


require_once __DIR__ . '/vendor/autoload.php';


// DI & bootstrap in one

(function (?\WP_Post $post) {
	// latte
	$latte = (new Engine())->setTempDirectory(__DIR__ . '/temp/cache/latte');

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
	$controllerFactory = new ControllerFactory(
		$configuration->get('dateFormat:human'),
		$configuration->get('dateFormat:robot'),
		new Client(
			$configuration->get('bis:url'),
			$configuration->get('bis:username'),
			$configuration->get('bis:password'),
			new HttpClient(),
		),
		new BaseFactory(),
		$latte,
		new GeocodingClientFacade(
			new CachedGeocodingService(
				new CacheManager(__DIR__ . '/temp/cache/geocoding'),
				new MapyCzGeocodingService(
					new Communicator(),
					new Mapper(),
				),
			),
		),
	);
	$controller = $controllerFactory->create($post); // routing is contained inside
	$controller->render();
})(get_post());
