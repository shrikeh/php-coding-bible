<?php

declare(strict_types=1);

namespace Shrikeh\Standards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Shrikeh\Standards\Sniffs\Traits\FixerTrait;
use Shrikeh\Standards\Sniffs\Traits\PositionTrait;

final class FinalClassesSniff implements Sniff
{
    use FixerTrait;
    use PositionTrait;

    public const FIXABLE_MSG = 'All classes should be declared using the "final" keyword';

    public const TOKENS = [
        T_FINAL,
    ];

    public function register(): array
    {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $this->setFixer($phpcsFile->fixer);
        $this->setPosition($stackPtr);

        if (!$phpcsFile->findPrevious($this->getTokens(), $stackPtr)) {
            $phpcsFile->addFixableError(
                self::FIXABLE_MSG,
                $stackPtr - 1,
                self::class
            );

            $this->fix();
        }
    }

    /**
     * @return array<int, int>
     */
    private function getTokens(): array
    {
        return self::TOKENS;
    }

    private function fix(): void
    {
        $this->fixer()->addContent($this->position() - 1, 'final ');
    }
}
