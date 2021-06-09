<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use Ecomail;


/**
 * Why there is this wrapper around Ecomail's official client?
 * Because we don't want to hard-code list's ID, we want to pass it from config once
 * and then don't bother with it.
 *
 * Also we don't need to expose more methods than it is needed.
 */
final class EcomailClient
{

	public function __construct(
		private int $brontowebContactsListId,
		private Ecomail $ecomail,
	) {}


	// it would be even better to accept an object instead of array, which would have ->toArray() method defining format for Ecomail API
	public function addSubscriber(array $data): array|\stdClass|string
	{
		return $this->ecomail->addSubscriber((string) $this->brontowebContactsListId, $data);
	}

}
