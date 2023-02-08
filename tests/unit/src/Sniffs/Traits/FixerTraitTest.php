<?php

declare(strict_types=1);

namespace Tests\Unit\Sniffs\Traits;

use PHPUnit\Framework\TestCase;
use Shrikeh\Standards\Sniffs\Exception\FixerNotSet;
use Shrikeh\Standards\Sniffs\Traits\FixerTrait;

final class FixerTraitTest extends TestCase
{
    public function testItThrowsAnExceptionIfTheFixerIsNotSet(): void
    {
        $fixerTrait = new class () {
            use FixerTrait;

            public function fix(): void
            {
                $this->fixer();
            }
        };

        $this->expectExceptionMessage(FixerNotSet::MSG);
        $this->expectException(FixerNotSet::class);

        $fixerTrait->fix();
    }
}
