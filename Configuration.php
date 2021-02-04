<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use Nette\Neon\Neon;


final class Configuration
{
	private const ROOT_KEY_PARAMETERS = 'parameters';

	private array $configuration = [];

	/**
	 * @throws \Exception
	 */
	public function __construct(array $configFiles)
	{
		foreach ($configFiles as $file) {
			if (!\file_exists($file)) {
				throw new \Exception('Config file `' . $file . '` does not exist.');
			}

			$fileContent = \file_get_contents($file);
			$configuration = Neon::decode($fileContent);

			if (!\array_key_exists(self::ROOT_KEY_PARAMETERS, $configuration)) {
				throw new \Exception('Only `' . self::ROOT_KEY_PARAMETERS . '` root key is supported in config files.');
			}

			$this->configuration = \array_merge($this->configuration, $configuration[self::ROOT_KEY_PARAMETERS]);
		}
	}


	/**
	 * Gets single item of configuration. You can use `:` separator to target nested keys.
	 * @throws \Exception
	 */
	public function get(string $name): mixed
	{
		$keyList = \explode(':', $name);
		$currentDimension = $this->configuration;
		foreach ($keyList as $key) {
			if (!\array_key_exists($key, $currentDimension)) {
				throw new \Exception('Key `' . $name . '` was not found in configuration.');
			}

			$value = $currentDimension[$key];
			$currentDimension = $currentDimension[$key];
		}

		return $value;
	}


	public function getAll(): array
	{
		return $this->configuration;
	}

}
