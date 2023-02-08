<?php

declare(strict_types=1);

namespace Shrikeh\Standards\Sniffs\Traits;

use PHP_CodeSniffer\Fixer;
use Shrikeh\Standards\Sniffs\Exception\FixerNotSet;

trait FixerTrait
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?Fixer $fixer;

    private function setFixer(Fixer $fixer): void
    {
        $this->fixer = $fixer;
    }


    private function fixer(): Fixer
    {
        if (!isset($this->fixer)) {
            throw FixerNotSet::create();
        }

        return $this->fixer;
    }
}
