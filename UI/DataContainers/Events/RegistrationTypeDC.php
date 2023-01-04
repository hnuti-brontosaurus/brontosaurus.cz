<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration\RegistrationQuestion;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration\RegistrationType;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use Nette\Utils\Strings;


/**
 * @property-read bool $isOfTypeNone
 * @property-read bool $isOfTypeBrontoWeb
 * @property-read string[]|null $questions
 * @property-read bool $isOfTypeEmail
 * @property-read string|null $email
 * @property-read bool $isOfTypeCustomWebpage
 * @property-read string|null $url
 * @property-read bool $isOfTypeDisabled
 */
final class RegistrationTypeDC
{
	use PropertyHandler;


	/**
	 * @param string[]|null $questions In case of registering via Brontoweb.
	 */
	private function __construct(
		private bool $isOfTypeNone,
		private bool $isOfTypeBrontoWeb,
		private ?array $questions,
		private bool $isOfTypeEmail,
		private ?string $email, // in case of registering via e-mail
		private bool $isOfTypeCustomWebpage,
		private ?string $url, // in case of registering via custom webpage
		private bool $isOfTypeDisabled,
	) {}


	public static function fromDTO(RegistrationType $registrationType): self
	{
		return new self(
			$registrationType->isOfTypeNone(),
			$registrationType->isOfTypeBrontoWeb(),
			$registrationType->isOfTypeBrontoWeb() ? \array_map(fn(RegistrationQuestion $question) => $question->toString(), $registrationType->getQuestions()) : null,
			$registrationType->isOfTypeEmail(),
			$registrationType->isOfTypeEmail() ? $registrationType->getEmail() : null,
			$registrationType->isOfTypeCustomWebpage(),
			$registrationType->isOfTypeCustomWebpage() ? self::fixUrl($registrationType->getUrl()) : null,
			$registrationType->isOfTypeDisabled(),
		);
	}


	private static function fixUrl(string $url): string
	{
		// if no protocol at the beginning, prefix it
		if ( ! Strings::startsWith($url, 'http')) {
			return 'http://' . $url;
		}

		return $url;
	}


	public function getLinkTarget(string $eventName): string
	{
		return match (true) {
			$this->isOfTypeEmail => 'mailto:' . $this->email . '?subject=Přihláška na akci ' . $eventName,
			$this->isOfTypeCustomWebpage => $this->url,
			default => '',
		};
	}

}
