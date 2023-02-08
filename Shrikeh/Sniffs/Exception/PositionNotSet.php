<?php

declare(strict_types=1);

namespace Shrikeh\Standards\Sniffs\Exception;

use RuntimeException;

final class PositionNotSet extends RuntimeException implements SniffStandardsException
{
    public const MSG = 'The Position was not set';

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        parent::__construct(self::MSG);
    }
}
