<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration;

use HnutiBrontosaurus\LegacyBisApiClient\BadUsageException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\RegistrationTypeException;


final class RegistrationType
{

	private bool $hasValidData;


	/**
	 * @param RegistrationQuestion[] $questions
	 */
	private function __construct(
		private RegistrationTypeEnum $type,
		private array $questions, // in case of registering via Brontoweb
		private ?string $email, // in case of registering via e-mail
		private ?string $url, // in case of registering via custom webpage
	) {
		$this->hasValidData = match (true) {
			$type->equals(RegistrationTypeEnum::EMAIL()) && $email === null,
			$type->equals(RegistrationTypeEnum::EXTERNAL_WEBPAGE()) && $url === null,
				=> false,
			default => true
		};
	}

	/**
	 * @param RegistrationQuestion[] $questions
	 */
	public static function from(
		RegistrationTypeEnum $type,
		array $questions,
		?string $email,
		?string $url,
	): self
	{
		return new self($type, $questions, $email, $url);
	}


	// registration via Brontoweb

	public function isOfTypeBrontoWeb(): bool
	{
		return $this->type->equals(RegistrationTypeEnum::BRONTOWEB());
	}


	public function areAnyQuestions(): bool
	{
		return \count($this->questions) > 0;
	}

	/**
	 * @return RegistrationQuestion[]
	 */
	public function getQuestions(): array
	{
		return $this->questions;
	}


	// registration via e-mail

	public function isOfTypeEmail(): bool
	{
		return $this->type->equals(RegistrationTypeEnum::EMAIL());
	}

	/**
	 * @throws RegistrationTypeException
	 * @throws BadUsageException
	 */
	public function getEmail(): ?string
	{
		if ( ! $this->hasValidData()) {
			throw RegistrationTypeException::missingAdditionalData('email', $this->type);
		}

		if ( ! $this->isOfTypeEmail()) {
			throw new BadUsageException('This method can not be called when the registration is not of `via e-mail` type.');
		}

		return $this->email;
	}


	// registration via custom webpage

	public function isOfTypeCustomWebpage(): bool
	{
		return $this->type->equals(RegistrationTypeEnum::EXTERNAL_WEBPAGE());
	}

	/**
	 * @throws RegistrationTypeException
	 * @throws BadUsageException
	 */
	public function getUrl(): ?string
	{
		if ( ! $this->hasValidData()) {
			throw RegistrationTypeException::missingAdditionalData('url', $this->type);
		}

		if ( ! $this->isOfTypeCustomWebpage()) {
			throw new BadUsageException('This method can not be called when the registration is not of `via custom webpage` type.');
		}

		return $this->url;
	}


	// no registration needed

	public function isOfTypeNone(): bool
	{
		return $this->type->equals(RegistrationTypeEnum::NONE());
	}


	// registration disabled

	public function isOfTypeDisabled(): bool
	{
		return $this->type->equals(RegistrationTypeEnum::DISABLED());
	}


	public function hasValidData(): bool
	{
		return $this->hasValidData;
	}

}
