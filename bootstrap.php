<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;


require __DIR__ . '/vendor/autoload.php';

return new Container(new Configuration([
	__DIR__ . '/config/config.neon',
	__DIR__ . '/config/config.local.neon',
]));
