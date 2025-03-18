<?php

namespace HnutiBrontosaurus\Theme;


require __DIR__ . '/vendor/autoload.php';

define('HB_IS_ON_PRODUCTION', $_SERVER['HTTP_HOST'] === 'brontosaurus.cz');

require __DIR__ . '/helpers.php';

return new Container(new Configuration([
	__DIR__ . '/config/config.neon',
	__DIR__ . '/config/config.local.neon',
]));
