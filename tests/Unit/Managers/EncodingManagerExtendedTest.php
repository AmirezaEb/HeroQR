<?php

namespace HeroQR\Tests\Unit\Managers;

use HeroQR\{Contracts\Managers\EncodingManagerInterface, Managers\EncodingManager};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class EncodingManagerExtendedTest
 * Extended coverage for EncodingManager supported encodings.
 */
class EncodingManagerExtendedTest extends TestCase
{
    private EncodingManager $encodingManager;

    /**
     * Initializes the EncodingManager instance
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->encodingManager = new EncodingManager();
        $this->assertInstanceOf(EncodingManagerInterface::class, $this->encodingManager);
    }

    /**
     * Provides additional valid encodings
     */
    public static function validEncodingProvider(): array
    {
        return [
            'ASCII' => ['ASCII'],
            'ISO-8859-1' => ['ISO-8859-1'],
            'ISO-8859-5' => ['ISO-8859-5'],
            'ISO-8859-15' => ['ISO-8859-15'],
            'UTF-16BE' => ['UTF-16BE'],
            'UTF-32' => ['UTF-32'],
        ];
    }

    /**
     * Test setting multiple valid encodings
     */
    #[Test]
    #[DataProvider('validEncodingProvider')]
    public function isSetAdditionalValidEncodings(string $encoding): void
    {
        $this->encodingManager->setEncoding($encoding);
        $this->assertSame($encoding, $this->encodingManager->getEncoding()->__toString());
    }

    /**
     * Test whitespace-only encoding is treated as empty
     */
    #[Test]
    public function isWhitespaceEncodingThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Encoding Cannot Be Empty');

        $this->encodingManager->setEncoding('');
    }

    /**
     * Test encoding can be changed multiple times
     */
    #[Test]
    public function isEncodingCanBeChangedMultipleTimes(): void
    {
        $this->encodingManager->setEncoding('ASCII');
        $this->assertSame('ASCII', $this->encodingManager->getEncoding()->__toString());

        $this->encodingManager->setEncoding('UTF-8');
        $this->assertSame('UTF-8', $this->encodingManager->getEncoding()->__toString());
    }
}
