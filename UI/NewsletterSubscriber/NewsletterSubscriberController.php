<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\NewsletterSubscriber;

use HnutiBrontosaurus\Theme\EcomailClient;
use HnutiBrontosaurus\Theme\UI\Controller;
use Nette\Http\Request;


final class NewsletterSubscriberController implements Controller
{
	public const PAGE_SLUG = 'prihlaska-k-newsletteru';
	public const EMAIL_ADDRESS_FIELD_KEY = 'emailAddress';

	public function __construct(
		private EcomailClient $ecomailClient,
		private Request $httpRequest,
	) {}


	public function render(): void
	{
		// todo
		$emailAddress = $this->httpRequest->getPost(self::EMAIL_ADDRESS_FIELD_KEY);
		$this->ecomailClient->addSubscriber([
			'email' => $emailAddress,
			'tags' => ['WEB'],
		]);

		// todo send payload with ok or error
	}

}
