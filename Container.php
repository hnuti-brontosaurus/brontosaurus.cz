<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use Grifart\GeocodingClient\Providers\Cache\CacheManager;
use Grifart\GeocodingClient\Providers\Cache\CacheProvider;
use Grifart\GeocodingClient\Providers\MapyCz\MapyCzProvider;
use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\BisClientFactory;
use HnutiBrontosaurus\Theme\CoordinatesResolver\CoordinatesResolver;
use HnutiBrontosaurus\Theme\UI\Base\BaseFactory;
use Latte\Engine;
use function is_dir;
use function mkdir;
use function sprintf;


final class Container
{

	public function __construct(
		private Configuration $configuration,
	) {}


	private ?ApplicationUrlTemplate $applicationUrlTemplate = null;
	public function getApplicationUrlTemplate(): ApplicationUrlTemplate
	{
		if ($this->applicationUrlTemplate !== null) {
			return $this->applicationUrlTemplate;
		}

		return $this->applicationUrlTemplate = ApplicationUrlTemplate::from($this->configuration->get('bis:applicationUrlTemplate'));
	}


	private ?BaseFactory $baseFactory = null;
	public function getBaseFactory(): BaseFactory
	{
		if ($this->baseFactory !== null) {
			return $this->baseFactory;
		}

		return $this->baseFactory = new BaseFactory($this->getEnableTracking());
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


	private ?CoordinatesResolver $coordinatesResolver = null;
	public function getCoordinatesResolver(): CoordinatesResolver
	{
		if ($this->coordinatesResolver !== null) {
			return $this->coordinatesResolver;
		}

		$cachePath = __DIR__ . '/temp/geocoding-cache';
		self::createDirectoryIfNeeded($cachePath);

		return $this->coordinatesResolver = new CoordinatesResolver(
			new CacheProvider(
				new CacheManager($cachePath),
				new MapyCzProvider(),
			),
		);
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


	private ?Engine $latte = null;
	public function getLatte(): Engine
	{
		if ($this->latte !== null) {
			return $this->latte;
		}

		$cachePath = __DIR__ . '/temp/cache/latte';
		self::createDirectoryIfNeeded($cachePath);

		$latte = new Engine();
		$latte->setTempDirectory($cachePath);

		return $this->latte = $latte;
	}


	public function getSentryDsn(): ?string
	{
		return $this->configuration->has('sentry:dsn') && ($dsn = $this->configuration->get('sentry:dsn')) !== ''
			? $dsn
			: null;
	}


	private static function createDirectoryIfNeeded(string $path): void
	{
		if (is_dir($path)) {
			return;
		}

		if ( ! @mkdir($path, recursive: true)) {
			throw new UsageException(sprintf("Could not create path '%s'", $path));
		}
	}

}
