<?php

namespace HeroQR\Tests\Managers;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use HeroQR\Managers\EncodingManager;
use PHPUnit\Framework\Attributes\Test;
use Endroid\QrCode\Encoding\EncodingInterface;

/**
 * Class EncodingManagerTest
 * Unit tests for the EncodingManager class.
 */
class EncodingManagerTest extends TestCase
{
    private EncodingManager $encodingManager;

    /**
     * Setup method
     * Initializes an instance of EncodingManager before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->encodingManager = new EncodingManager();
    }

    /**
     * Test the getEncoding method
     * Ensures the default encoding is 'UTF-8' and is an instance of EncodingInterface.
     */
    #[Test]
    public function isGetEncoding()
    {
        $labelEncoding = $this->encodingManager->getEncoding();

        $this->assertInstanceOf(EncodingInterface::class, $labelEncoding, 'Encoding Should Be An Instance Of EncodingInterface.');
        $this->assertEquals('UTF-8', $labelEncoding->__toString(), 'The Default Encoding Should Be UTF-8.');
    }

    /**
     * Test the setEncoding method with a valid value
     * Verifies that a valid encoding, such as 'UTF-16', updates the encoding properly.
     */
    #[Test]
    public function isSetEncodingValid()
    {
        $this->encodingManager->setEncoding('UTF-16');
        $labelEncoding = $this->encodingManager->getEncoding();

        $this->assertInstanceOf(EncodingInterface::class, $labelEncoding, 'Encoding Should Be An Instance Of EncodingInterface');
        $this->assertEquals('UTF-16', $labelEncoding->__toString(), 'Encoding Should Change To The Set Value');
    }

    /**
     * Test the setEncoding method with an empty value
     * Ensures that providing an empty string throws an InvalidArgumentException.
     */
    #[Test]
    public function isSetEncodingEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Encoding Cannot Be Empty');

        $this->encodingManager->setEncoding('');
    }

    /**
     * Test the setEncoding method with an invalid value
     * Ensures that an invalid encoding value, such as 'INVALID_ENCODING', throws an exception.
     */
    #[Test]
    public function isSetEncodingInvalid()
    {
        $this->expectException(Exception::class);

        $this->encodingManager->setEncoding('INVALID_ENCODING');
    }

    /**
     * Test the setEncoding method with multiple valid values
     * Checks that the method correctly handles several valid encoding values like 'UTF-32LE' and 'EUC-KR'.
     */
    #[Test]
    public function isSetEncodingValidValues()
    {
        $validEncodings = ['UTF-32LE', 'EUC-KR'];

        foreach ($validEncodings as $encodingValue) {
            $this->encodingManager->setEncoding($encodingValue);
            $labelEncoding = $this->encodingManager->getEncoding();

            $this->assertInstanceOf(EncodingInterface::class, $labelEncoding, "Encoding Should Be An Instance Of EncodingInterface");
            $this->assertEquals($encodingValue, $labelEncoding->__toString(), "Encoding Should Be Set To $encodingValue.");
        }
    }
}
