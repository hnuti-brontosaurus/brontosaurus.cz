<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Request;


final class Adoption extends Parameters
{

	public function __construct(
		int $amount,
		string $firstName,
		string $lastName,
		string $streetAddress,
		string $streetNumber,
		string $postalCode,
		string $city,
		string $emailAddress,
		?int $preferredUnitOfTypeRegional,
		?int $preferredUnitOfTypeBase,
		bool $excludeFromPublic,
	) {
		parent::__construct([
			self::PARAM_QUERY => 'adopce',
			'f_jmeno' => $firstName,
			'f_prijmeni' => $lastName,
			'f_ulice' => $streetAddress . ' ' . $streetNumber,
			'f_mesto' => $city,
			'f_psc' => $postalCode,
			'f_email' => $emailAddress,
			'f_pohlavi' => null, // not required, but accepted by BIS (values muz/zena)
			'f_uvest_v_seznamu' => $excludeFromPublic ? 'off' : 'on',
			'f_clanek' => $preferredUnitOfTypeBase,
			'f_rc' => $preferredUnitOfTypeRegional,
			'f_castka' => $amount,
		]);
	}

}
