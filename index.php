<?php declare(strict_types = 1);

use GuzzleHttp\Client as HttpClient;
use HnutiBrontosaurus\BisApiClient\Client;
use HnutiBrontosaurus\Theme\Configuration;
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
	$latte = (new Engine())->setTempDirectory(__DIR__ . '/temp');

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
	);
	$controller = $controllerFactory->create($post); // routing is contained inside
	$controller->render();
})(get_post());
