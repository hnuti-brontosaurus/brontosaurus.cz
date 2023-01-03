<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

class UsageException extends \LogicException {} // exceptions caused by bad usage by developer (preventable) – e.g. called method in a wrong moment etc.
class RuntimeException extends \RuntimeException {} // exceptions caused by unpredictable circumstances on server (not preventable) – e.g. file does not exist, external system not accessible etc.

final class CannotResolveCoordinates extends RuntimeException {}
final class NotFound extends RuntimeException {}
