<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;


final class SentryLogger
{

	private function __construct(private bool $nullLogger)
	{}

	public static function initialize(?string $dsn): self
	{
		// no DSN provided, we will work as null logger
		if ($dsn === null) {
			return new self(nullLogger: true);
		}

		\Sentry\init(['dsn' => $dsn]);
		return new self(nullLogger: false);
	}

	public function captureMessage(string $message): void
	{
		\Sentry\captureMessage($message);
	}

}
