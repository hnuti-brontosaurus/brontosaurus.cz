<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient;


// overall exceptions

abstract class BisApiClientLogicException extends \LogicException
{}

abstract class BisApiClientRuntimeException extends \RuntimeException
{}


// working with API exceptions

final class InvalidArgumentException extends BisApiClientLogicException
{}

final class BadUsageException extends BisApiClientLogicException
{}


// communicating with BIS exceptions

abstract class ConnectionException extends BisApiClientRuntimeException
{}

final class TransferErrorException extends ConnectionException
{}

final class NotFoundException extends ConnectionException
{}
