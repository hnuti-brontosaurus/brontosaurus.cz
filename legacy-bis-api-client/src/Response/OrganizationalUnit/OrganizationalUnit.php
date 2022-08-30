<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\OrganizationalUnit;

use Grifart\Enum\MissingValueDeclarationException;


final class OrganizationalUnit
{

	private function __construct(
		private int $id,
		private string $name,
		private string $street,
		private string $city,
		private string $postCode,
		private ?string $phone,
		private ?string $email,
		private ?string $website,
		private OrganizationalUnitType $type,
		private ?string $chairman,
		private ?string $manager,
	) {}


	/**
	 * @throws UnknownOrganizationUnitTypeException
	 */
	public static function fromResponseData(array $data): self
	{
		try {
			$organizationalUnitTypeScalar = (int) $data['uroven'];
			$organizationalUnitType = OrganizationalUnitType::fromScalar($organizationalUnitTypeScalar);

		} catch (MissingValueDeclarationException) {
			throw new UnknownOrganizationUnitTypeException('Type `' . $organizationalUnitTypeScalar . '` is not of valid types.');
		}

		return new self(
			(int) $data['id'],
			$data['nazev'],
			$data['ulice'],
			$data['mesto'],
			$data['psc'],
			(isset($data['telefon']) && $data['telefon'] !== '') ? $data['telefon'] : null,
			(isset($data['email']) && $data['email'] !== '') ? $data['email'] : null,
			(isset($data['www']) && $data['www'] !== '') ? $data['www'] : null,
			$organizationalUnitType,
			(isset($data['predseda']) && $data['predseda'] !== '') ? $data['predseda'] : null,
			(isset($data['hospodar']) && $data['hospodar'] !== '') ? $data['hospodar'] : null,
		);
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getStreet(): string
	{
		return $this->street;
	}


	public function getCity(): string
	{
		return $this->city;
	}


	public function getPostCode(): string
	{
		return $this->postCode;
	}


	public function getPhone(): ?string
	{
		return $this->phone;
	}


	public function getEmail(): ?string
	{
		return $this->email;
	}


	public function getWebsite(): ?string
	{
		return $this->website;
	}


	public function getChairman(): ?string
	{
		return $this->chairman;
	}


	public function getManager(): ?string
	{
		return $this->manager;
	}


	public function isClub(): bool
	{
		return $this->type->equals(OrganizationalUnitType::CLUB());
	}


	public function isBaseUnit(): bool
	{
		return $this->type->equals(OrganizationalUnitType::BASE());
	}


	public function isRegionalUnit(): bool
	{
		return $this->type->equals(OrganizationalUnitType::REGIONAL());
	}


	public function isOffice(): bool
	{
		return $this->type->equals(OrganizationalUnitType::OFFICE());
	}

}
