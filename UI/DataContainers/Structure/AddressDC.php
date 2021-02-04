<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Structure;

use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $street
 * @property-read string $city
 * @property-read string $postCode
 */
final class AddressDC
{

	use PropertyHandler;


	/** @var string */
	private $street;

	/** @var string */
	private $city;

	/** @var string */
	private $postCode;


	/**
	 * @param string $street
	 * @param string $city
	 * @param string $postCode
	 */
	private function __construct($street, $city, $postCode)
	{
		$this->street = $street;
		$this->city = $city;
		$this->postCode = $postCode;
	}


	/**
	 * @param string $street
	 * @param string $city
	 * @param string $postCode
	 *
	 * @return AddressDC
	 */
	public static function from($street, $city, $postCode)
	{
		return new self($street, $city, $postCode);
	}

}
