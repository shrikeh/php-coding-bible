<?php

declare(strict_types=1);

namespace Tests\Unit\Sniffs\Traits;

use Shrikeh\Standards\Sniffs\Exception\FixerNotSet;
use Shrikeh\Standards\Sniffs\Exception\PositionNotSet;
use Shrikeh\Standards\Sniffs\Traits\FixerTrait;
use Shrikeh\Standards\Sniffs\Traits\PositionTrait;
use PHPUnit\Framework\TestCase;

final class PositionTraitTest extends TestCase
{
    public function testItThrowsAnExceptionIfThePositionIsNotSet(): void
    {
        $positionTrait = new class () {
            use PositionTrait;

            public function throw(): void
            {
                $this->position();
            }
        };

        $this->expectExceptionMessage(PositionNotSet::MSG);
        $this->expectException(PositionNotSet::class);

        $positionTrait->throw();
    }
}
