<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Request;

use HnutiBrontosaurus\LegacyBisApiClient\InvalidArgumentException;


final class OrganizationalUnitParameters extends Parameters
{

	const PARAM_FILTER = 'filter';

	const TYPE_CLUB = 'klub';
	const TYPE_BASE = 'zc';
	const TYPE_REGIONAL = 'rc';


	public function __construct()
	{
		parent::__construct([
			self::PARAM_QUERY => 'zc'
		]);
	}


	/**
	 * @throws InvalidArgumentException
	 */
	public function setType(string $type): static
	{
		if (!\in_array($type, [
			self::TYPE_CLUB,
			self::TYPE_BASE,
			self::TYPE_REGIONAL,
		], true)) {
			throw new InvalidArgumentException('Type `' . $type . '` is not of valid types.');
		}

		$this->params[self::PARAM_FILTER] = $type;

		return $this;
	}

}
