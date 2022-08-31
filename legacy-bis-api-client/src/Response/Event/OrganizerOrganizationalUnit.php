<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event;


/**
 * This could be OrganizationalUnit instead as well,
 * but one more request per each event would be generated which is kinda wasteful.
 * And Brontosaurus does not want any more waste... :-)
 */
final class OrganizerOrganizationalUnit
{

	private function __construct(
		private int $id,
		private string $name,
	) {}


	public static function from(int $id, string $name): self
	{
		return new self($id, $name);
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function getName(): string
	{
		return $this->name;
	}

}
