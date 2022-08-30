<?php declare(strict_types = 1);

/**
 * See https://bis.brontosaurus.cz/myr.php for more information on values.
 */

namespace HnutiBrontosaurus\LegacyBisApiClient\Request;


abstract class Parameters
{

	const PARAM_USERNAME = 'user';
	const PARAM_PASSWORD = 'password';

	const PARAM_QUERY = 'query';


	protected array $params = [];


	/**
	 * Currently no limitations to what can be in `params`.
	 */
	public function __construct(array $params = [])
	{
		$this->params = $params;
	}


	public function getAll(): array
	{
		return $this->params;
	}


	public function getQueryString(): string
	{
		return \http_build_query($this->params);
	}


	public function setCredentials(string $username, string $password): static
	{
		$this->params[self::PARAM_USERNAME] = $username;
		$this->params[self::PARAM_PASSWORD] = $password;

		return $this;
	}

}
