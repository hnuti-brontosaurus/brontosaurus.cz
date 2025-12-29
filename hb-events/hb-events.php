<?php

use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Tag;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use Tracy\Debugger;

function hb_events(Container $container)
{
    register_block_type(__DIR__, [
        'render_callback' => function ($block_attributes, $content) use ($container) {
            $bisClient = $container->getBisClient();

            $params = new EventParameters();
            $selected = $block_attributes['tags'] ?? null;
            if ($selected !== null && $selected !== '') {
                $params->setTag(Tag::fromScalar($selected));
            }

            try {
                $events = $bisClient->getEvents($params);

            } catch (ConnectionToBisFailed $e) {
                Debugger::log($e);
                return "error loading events from BIS";
            }

            ob_start();
            hb_eventList(new EventCollectionDC(
                $events,
                $container->getDateFormatForHuman(),
                $container->getDateFormatForRobot(),
            ), lazyLoading: false);
            $content = ob_get_contents();
		    ob_end_clean();
            return $content;
        },
    ]);
}
