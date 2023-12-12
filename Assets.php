<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use WP_Theme;
use function filemtime;
use function sprintf;
use function wp_enqueue_script;
use function wp_enqueue_style;


final class Assets
{

	public static function script(string $name, WP_Theme $theme): void
	{
		wp_enqueue_script(
			handle: 'hb-' . $name,
			src: self::src($name, self::JS_PATTERN, $theme),
			ver: self::ver($name, self::JS_PATTERN, $theme),
		);
	}

	public static function style(string $name, WP_Theme $theme): void
	{
		wp_enqueue_style(
			handle: 'hb-' . $name,
			src: self::src($name, self::CSS_PATTERN, $theme),
			ver: self::ver($name, self::CSS_PATTERN, $theme),
		);
	}

	private const CSS_PATTERN = '%s/frontend/dist/css/%s.css';
	private const JS_PATTERN = '%s/frontend/dist/js/%s.js';

	private static function src(string $for, string $pattern, WP_Theme $theme): string
	{
		return sprintf($pattern, $theme->get_template_directory_uri(), $for);
	}

	private static function ver(string $for, string $pattern, WP_Theme $theme): int
	{
		$path = sprintf($pattern, $theme->get_template_directory(), $for);
		return filemtime($path);
	}

}
