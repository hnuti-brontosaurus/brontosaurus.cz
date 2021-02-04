<?php declare(strict_types = 1);

use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use HnutiBrontosaurus\Theme\UI\ControllerFactory;
use Latte\Bridges\Tracy\BlueScreenPanel;
use Latte\Bridges\Tracy\LattePanel;
use Latte\Engine;
use Tracy\Debugger;


require_once __DIR__ . '/vendor/autoload.php';


// DI & bootstrap in one

(function (\WP_Post $post) {
	// latte
	$latte = (new Engine())->setTempDirectory(__DIR__ . '/temp');

	// tracy
	Debugger::$logDirectory = __DIR__ . '/log';
	Debugger::$strictMode = true;
	Debugger::enable();
	BlueScreenPanel::initialize();
	LattePanel::initialize($latte);

	// app
	$controllerFactory = new ControllerFactory(
		new BaseFactory(),
		$latte,
	);
	$controller = $controllerFactory->create($post); // routing is contained inside
	$controller->render();
})(get_post());
