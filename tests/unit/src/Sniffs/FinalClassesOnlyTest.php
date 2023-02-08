<?php

declare(strict_types=1);

namespace Tests\Unit\Sniffs;

use Shrikeh\Standards\Sniffs\FinalClassesSniff;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;
use SplFileInfo;
use Tests\Mock\BadClass;
use Tests\Mock\WellFormedClass;
use Tests\Utils\Constants;

final class FinalClassesOnlyTest extends TestCase
{
    use ProphecyTrait;

    public function testItCanRegister(): void
    {
        $finalClassesSniff = new FinalClassesSniff();

        $this->assertSame([T_CLASS], $finalClassesSniff->register());
    }

    public function testItProcessesAFinalClass(): void
    {
        $testFile = $this->getClassFileInfo(WellFormedClass::class);
        $sniffFile = $this->getClassFileInfo(FinalClassesSniff::class);
        $file = $this->createFile($testFile, $sniffFile);
        $file->process();

        $this->assertCount(0, $file->getErrors());
    }

    public function testItProcessesAClassNotMarkedAsFinal(): void
    {
        $stackPtr = 21;
        $previous = $stackPtr - 1;
        $file = $this->prophesize(File::class);
        $file->findPrevious(FinalClassesSniff::TOKENS, $stackPtr)->willReturn(false);
        $file->addFixableError(
            FinalClassesSniff::FIXABLE_MSG,
            $previous,
            FinalClassesSniff::class
        )->shouldBeCalled();

        $fixer = $this->prophesize(Fixer::class);
        $fixer->addContent($previous, 'final ')->shouldBeCalled();
        $file->fixer = $fixer->reveal();

        $sniff = new FinalClassesSniff();
        $sniff->process($file->reveal(), $stackPtr);
    }

    public function testItCanFixAClass(): void
    {
        $sniffFile = $this->getClassFileInfo(FinalClassesSniff::class);
        $badClassFile = $this->getClassFileInfo(BadClass::class);

        $file = $this->createFile($badClassFile, $sniffFile);
        $file->process();
        $diff = $file->fixer->generateDiff();

        $this->assertStringContainsString('-class BadClass', $diff);
        $this->assertStringContainsString('+final class BadClass', $diff);
    }

    private function createFile(SplFileInfo $classToSniff, SplFileInfo $sniffClass): File
    {
        $config = new Config();
        $config->standards = [Constants::srcDir() . '/ruleset.xml'];

        $ruleset = new Ruleset($config);
        $ruleset->registerSniffs(
            [$sniffClass->getRealPath()],
            [],
            [],
        );

        $ruleset->populateTokenListeners();


        return new LocalFile($classToSniff->getRealPath(), $ruleset, $config);
    }

    private function getClassFileInfo(string $className): SplFileInfo
    {
        $reflector = new ReflectionClass($className);

        return new SplFileInfo($reflector->getFileName());
    }
}
