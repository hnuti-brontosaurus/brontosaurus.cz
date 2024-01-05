<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\Events;

use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Tag;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventCollectionDC;
use function register_block_type;


final class Events
{

	public static function init(Container $container): void
	{
		register_block_type(__DIR__, [
			'render_callback' => function ($block_attributes, $content) use ($container) {
				$bisClient = $container->getBisClient();
				$params = self::prepareParams($block_attributes);
				$events = $bisClient->getEvents($params);

				$latte = $container->getLatte();

				return $latte->renderToString(__DIR__ . '/events.latte', [
					'eventCollection' => new EventCollectionDC(
						$events,
						$container->getDateFormatForHuman(),
						$container->getDateFormatForRobot(),
					),
					'lazyLoading' => false,
				]);
			},
		]);
	}

	/**
	 * @param array<'tags', mixed> $block_attributes
	 */
	private static function prepareParams(array $block_attributes): EventParameters
	{
		$params = new EventParameters();

		$selected = $block_attributes['tags'] ?? null;
		if ($selected !== null && $selected !== '') {
			$params->setTag(Tag::fromScalar($selected));
		}

		return $params;
	}

}
