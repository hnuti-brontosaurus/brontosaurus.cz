<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response;

use HnutiBrontosaurus\LegacyBisApiClient\BisApiClientRuntimeException;


abstract class ResponseErrorException extends BisApiClientRuntimeException
{}


// invalid data structure

final class InvalidContentTypeException extends ResponseErrorException
{}

final class InvalidXMLStructureException extends ResponseErrorException
{}


// invalid data values

final class InvalidParametersException extends ResponseErrorException
{}

final class InvalidUserInputException extends ResponseErrorException
{

	/**
	 * @param \DOMElement $element
	 */
	public function __construct(\DOMElement $element)
	{
		parent::__construct($element->nodeValue);
	}

}

final class UnauthorizedAccessException extends ResponseErrorException
{

	public function __construct()
	{
		parent::__construct('You are not authorized to make such request with given credentials. Or you have simply wrong credentials. :-)');
	}

}

final class UnknownErrorException extends ResponseErrorException
{

	/**
	 * @param string $unknownErrorTypeKey
	 */
	public function __construct($unknownErrorTypeKey)
	{
		parent::__construct('Unknown error. Error type returned from BIS: ' . $unknownErrorTypeKey);
	}

}

final class RegistrationTypeException extends ResponseErrorException
{

	public static function missingAdditionalData($key, $type)
	{
		return new self(\sprintf('Missing additional data `%s` for selected type %d.', $key, $type));
	}

}
