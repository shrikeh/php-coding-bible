<?php

declare(strict_types=1);

namespace Shrikeh\Standards\Sniffs\Traits;

use Shrikeh\Standards\Sniffs\Exception\PositionNotSet;

trait PositionTrait
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?int $position;

    private function setPosition(int $position): void
    {
        $this->position = $position;
    }

    private function position(): int
    {
        if (!isset($this->position)) {
            throw PositionNotSet::create();
        }

        return $this->position;
    }
}
