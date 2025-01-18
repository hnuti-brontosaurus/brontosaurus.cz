<?php

namespace HnutiBrontosaurus\Theme;


require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/helpers.php';

return new Container(new Configuration([
	__DIR__ . '/config/config.neon',
	__DIR__ . '/config/config.local.neon',
]));
