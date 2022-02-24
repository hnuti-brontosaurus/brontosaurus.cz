<?php declare(strict_types = 1);

use HnutiBrontosaurus\Theme\Configuration;


require_once __DIR__ . '/vendor/autoload.php';


function hb_getConfiguration(): Configuration
{
	return new Configuration([
		__DIR__ . '/config/config.neon',
		__DIR__ . '/config/config.local.neon',
	]);
}
