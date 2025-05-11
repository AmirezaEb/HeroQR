<?php

namespace HeroQR\Tests\Unit\Managers;

use PHPUnit\Framework\{Attributes\Test, TestCase};
use HeroQR\{Contracts\Managers\EncodingManagerInterface,Managers\EncodingManager};

/**
 * Class EncodingManagerTest
 * Unit tests for the EncodingManager class.
 */
class EncodingManagerTest extends TestCase
{
    private EncodingManager $encodingManager;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->encodingManager = new EncodingManager();

        $this->assertInstanceOf(EncodingManagerInterface::class, $this->encodingManager,'Failed to initialize EncodingManagerInterface instance');
    }

    /**
     * Test the getEncoding method
     */
    #[Test]
    public function isGetEncoding(): void
    {
        $labelEncoding = $this->encodingManager->getEncoding();

        $this->assertNotNull($labelEncoding, 'Encoding should not be null');
        $this->assertEquals('UTF-8', $labelEncoding->__toString(), 'The Default Encoding Should Be UTF-8');
    }

    /**
     * Test the setEncoding method with a valid value
     */
    #[Test]
    public function isSetEncodingValid(): void
    {
        $this->encodingManager->setEncoding('UTF-16');
        $labelEncoding = $this->encodingManager->getEncoding();

        $this->assertNotEquals('UTF-18', $labelEncoding->__toString());
        $this->assertEquals('UTF-16', $labelEncoding->__toString(), 'Encoding Should Change To The Set Value');
    }

    /**
     * Test the setEncoding method with an empty value
     */
    #[Test]
    public function isSetEncodingEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Encoding Cannot Be Empty');

        $this->encodingManager->setEncoding('');
    }

    /**
     * Test the setEncoding method with an invalid value
     */
    #[Test]
    public function isSetEncodingInvalid(): void
    {
        $this->expectException(\Exception::class);

        $this->encodingManager->setEncoding('INVALID_ENCODING');
    }

    /**
     * Test the setEncoding method with multiple valid values
     */
    #[Test]
    public function isSetEncodingValidValues(): void
    {
        $validEncodings = ['UTF-32LE', 'EUC-KR'];

        foreach ($validEncodings as $encodingValue) {
            $this->encodingManager->setEncoding($encodingValue);
            $labelEncoding = $this->encodingManager->getEncoding();

            $this->assertEquals($encodingValue, $labelEncoding->__toString(), "Encoding Should Be Set To $encodingValue");
        }
    }
}
