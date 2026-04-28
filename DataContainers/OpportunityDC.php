<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\Opportunity\Response\Opportunity;


final class OpportunityDC
{
	public readonly string $title;
	public readonly string $introduction;
	public readonly string $link;
	public readonly string $coverPhotoPath;

	public function __construct(Opportunity $opportunity)
	{
		$this->title = hb_handleNonBreakingSpaces($opportunity->getName());

		$this->introduction = hb_handleNonBreakingSpaces((string) $opportunity->getIntroduction());

		$this->link = sprintf('%s/%s/%d/', // todo: use rather WP routing somehow
			rtrim(get_site_url(), '/'),
			'prilezitost',
			$opportunity->getId(),
		);

		$this->coverPhotoPath = $opportunity->getImage()->getMediumSizePath(); // todo small?
	}

}
