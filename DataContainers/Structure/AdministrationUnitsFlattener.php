<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Structure;

use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit as AdministrationUnitFromClient;


final class AdministrationUnitsFlattener
{

	/**
	 * @param AdministrationUnitFromClient[] $administrationUnits
	 * @return AdministrationUnit[]
	 */
	public static function flatten(array $administrationUnits): array
	{
		$flattened = [];
		foreach ($administrationUnits as $administrationUnit) {
			$flattened[] = AdministrationUnit::fromUnit($administrationUnit);
			foreach ($administrationUnit->getSubUnits() as $subUnit) {
				$flattened[] = AdministrationUnit::fromSubUnit($subUnit, $administrationUnit->getName());
			}
		}
		return $flattened;
	}

}
