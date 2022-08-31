<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration\RegistrationQuestion;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration\RegistrationType;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use Nette\Utils\Strings;


/**
 * @property-read bool $isOfTypeNone
 * @property-read bool $isOfTypeBrontoWeb
 * @property-read string[] $questions
 * @property-read bool $isOfTypeEmail
 * @property-read string|null $email
 * @property-read bool $isOfTypeCustomWebpage
 * @property-read string|null $url
 * @property-read bool $isOfTypeDisabled
 */
final class RegistrationTypeDC
{
	use PropertyHandler;


	/** @var bool */
	private $isOfTypeNone = FALSE;

	/** @var bool */
	private $isOfTypeBrontoWeb = FALSE;

	/** @var string[] In case of registering via Brontoweb. */
	private $questions = [];

	/** @var bool */
	private $isOfTypeEmail = FALSE;

	/** @var string|null In case of registering via e-mail. */
	private $email;

	/** @var bool */
	private $isOfTypeCustomWebpage = FALSE;

	/** @var string|null In case of registering via custom webpage. */
	private $url;

	/** @var bool */
	private $isOfTypeDisabled = FALSE;


	/**
	 * @param string[]|null $questions
	 */
	private function __construct(
		bool $isOfTypeNone,
		bool $isOfTypeBrontoWeb,
		?array $questions,
		bool $isOfTypeEmail,
		?string $email,
		bool $isOfTypeCustomWebpage,
		?string $url,
		bool $isOfTypeDisabled,
	) {
		if ($isOfTypeNone) {
			$this->isOfTypeNone = TRUE;
		}

		if ($isOfTypeBrontoWeb) {
			$this->isOfTypeBrontoWeb = TRUE;
			$this->questions = $questions;
		}

		if ($isOfTypeEmail) {
			$this->isOfTypeEmail = TRUE;
			$this->email = $email;
		}

		if ($isOfTypeCustomWebpage) {
			$this->isOfTypeCustomWebpage = TRUE;

			if ( ! Strings::startsWith($url, 'http')) { // if no protocol at the beginning, prefix it
				$url = 'http://' . $url;
			}

			$this->url = $url;
		}

		if ($isOfTypeDisabled) {
			$this->isOfTypeDisabled = TRUE;
		}
	}

	public static function fromDTO(RegistrationType $registrationType)
	{
		return new self(
			$registrationType->isOfTypeNone(),
			$registrationType->isOfTypeBrontoWeb(),
			$registrationType->hasValidData() && $registrationType->isOfTypeBrontoWeb() ? \array_map(function (RegistrationQuestion $question) { return $question->toString(); }, $registrationType->getQuestions()) : NULL,
			$registrationType->isOfTypeEmail(),
			$registrationType->hasValidData() && $registrationType->isOfTypeEmail() ? $registrationType->getEmail() : NULL,
			$registrationType->isOfTypeCustomWebpage(),
			$registrationType->hasValidData() && $registrationType->isOfTypeCustomWebpage() ? $registrationType->getUrl() : NULL,
			$registrationType->isOfTypeDisabled()
		);
	}


	/**
	 * @param string $eventName For email use-case.
	 * @return string
	 */
	public function getLinkTarget($eventName)
	{
		switch (TRUE) {
			case $this->isOfTypeEmail:
				return 'mailto:' . $this->email . '?subject=Přihláška na akci ' . $eventName;

			case $this->isOfTypeCustomWebpage:
				return $this->url;

			default:
				return '';
		}
	}

}
