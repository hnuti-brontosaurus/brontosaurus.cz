<?php

use HnutiBrontosaurus\Theme\Container;

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

            $events = $bisClient->getEvents($params);

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
