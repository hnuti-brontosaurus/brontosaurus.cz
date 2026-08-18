<?php

namespace HnutiBrontosaurus\Theme;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\BisClientFactory;


final class Container
{

	public function __construct(
		private Configuration $configuration,
	) {}


	public function getConfiguration(): Configuration
	{
		return $this->configuration;
	}


	private ?BisClient $bisClient = null;
	public function getBisClient(): BisClient
	{
		if ($this->bisClient !== null) {
			return $this->bisClient;
		}

		$factory = new BisClientFactory($this->configuration->get('bis:url'));
		return $this->bisClient = $factory->create();
	}


	public function getDateFormatForHuman(): string
	{
		return $this->configuration->get('dateFormat:human');
	}

	public function getDateFormatForRobot(): string
	{
		return $this->configuration->get('dateFormat:robot');
	}

	public function getDebugMode(): bool
	{
		return $this->configuration->get('debugMode');
	}

	public function getEnableTracking(): bool
	{
		return $this->configuration->get('enableTracking');
	}

}
