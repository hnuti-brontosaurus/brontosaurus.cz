<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\Opportunity\Response\Opportunity as OpportunityFromClient;


final readonly class Opportunity
{
	public string $title;
	public string $introduction;
	public string $link;
	public string $coverPhotoPath;

	public function __construct(OpportunityFromClient $opportunity)
	{
		$this->title = hb_handleNonBreakingSpaces($opportunity->name);

		$this->introduction = hb_handleNonBreakingSpaces((string) $opportunity->introduction);

		$this->link = sprintf('%s/%s/%d/', // todo: use rather WP routing somehow
			rtrim(get_site_url(), '/'),
			'prilezitost',
			$opportunity->id,
		);

		$this->coverPhotoPath = $opportunity->image->mediumSizePath; // todo small?
	}

}
