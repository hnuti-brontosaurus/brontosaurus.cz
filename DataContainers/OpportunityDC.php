<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\Opportunity\Response\Opportunity;


final /*readonly*/ class OpportunityDC
{
	public string $title;
	public string $introduction;
	public string $link;
	public string $coverPhotoPath;

	public function __construct(Opportunity $opportunity)
	{
		$this->title = hb_handleNonBreakingSpaces($opportunity->getName());
		$this->introduction = (string) $opportunity->getIntroduction();

		$this->link = sprintf('%s/%s/%d/', // todo: use rather WP routing somehow
			rtrim(get_site_url(), '/'),
			'prilezitost',
			$opportunity->getId(),
		);

		$this->coverPhotoPath = $coverPhotoPath->getImage()->getMediumSizePath(); // todo small?
	}

}
