<?php

namespace HeroQR\Tests\Unit\Core;

use Endroid\QrCode\Matrix\Matrix;
use HeroQR\{Core\QRCodeGenerator, DataTypes\DataType};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};
use http\Exception\InvalidArgumentException;

/**
 * Class QRCodeGeneratorTest
 * Tests the functionality of the QRCodeGenerator class
 */
class QRCodeGeneratorTest extends TestCase
{
    private QRCodeGenerator $qrCodeGenerator;

    /**
     * Set up the QRCodeGenerator instance before each test
     */
    protected function setUp(): void
    {
        $this->qrCodeGenerator = new QRCodeGenerator();
    }

    /**
     * Test setting valid data for QR code generation
     */
    #[Test]
    public function isSetDataValid(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->assertEquals('https://example.com', $this->qrCodeGenerator->getData());
    }

    /**
     * Test setting invalid data for QR code generation
     */
    #[Test]
    public function isSetDataInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setData('invalid-url', DataType::Url);
    }

    /**
     * Test setting a valid size for the QR code
     */
    #[Test]
    public function isSetSizeValid(): void
    {
        $this->qrCodeGenerator->setSize(300);
        $this->assertEquals(300, $this->qrCodeGenerator->getSize());
    }

    /**
     * Test setting an invalid size for the QR code
     */
    #[Test]
    public function isSetSizeInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setSize(-50);
    }

    /**
     * Test setting a valid margin for the QR code
     */
    #[Test]
    public function isSetMarginValid(): void
    {
        $this->qrCodeGenerator->setMargin(20);
        $this->assertEquals(20, $this->qrCodeGenerator->getMargin());
    }

    /**
     * Test setting an invalid margin for the QR code
     */
    #[Test]
    public function isSetMarginInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setMargin(-10);
    }

    /**
     * Test setting a valid color for the QR code
     */
    #[Test]
    public function isSetColorValid(): void
    {
        $this->qrCodeGenerator->setColor('#FF5733');
        $color = $this->qrCodeGenerator->getColor();
        $this->assertEquals([255, 87, 51, 0], [$color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()]);
    }

    /**
     * Test setting an invalid color for the QR code
     */
    #[Test]
    public function isSetColorInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setColor('invalid-color');
    }

    /**
     * Test setting a valid background color for the QR code
     */
    #[Test]
    public function isSetBackgroundColorValid(): void
    {
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');
        $color = $this->qrCodeGenerator->getBackgroundColor();
        $this->assertEquals([255, 255, 255, 0], [$color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()]);
    }

    /**
     * Test setting an invalid background color for the QR code
     */
    #[Test]
    public function isSetBackgroundColorInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setBackgroundColor('invalid-color');
    }

    /**
     * Test setting a valid logo for the QR code
     */
    #[Test]
    public function isSetLogoValid(): void
    {
        $logoPath = __DIR__ . '/test_logo.png';
        file_put_contents($logoPath, '');
        $this->qrCodeGenerator->setLogo($logoPath, 50);
        $this->assertEquals($logoPath, $this->qrCodeGenerator->getLogoPath());
        unlink($logoPath);
    }

    /**
     * Test setting an invalid logo for the QR code
     */
    #[Test]
    public function isSetLogoInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setLogo('invalid/path/to/logo.png', 50);
    }

    /**
     * Test setting a valid label for the QR code
     */
    #[Test]
    public function isSetLabelValid(): void
    {
        $this->qrCodeGenerator->setLabel('Test Label', 'center', '#000000', 20, [0, 10, 10, 10]);
        $this->assertEquals('Test Label', $this->qrCodeGenerator->getLabel());
    }

    /**
     * Test getting the matrix after generating the QR code
     */
    #[Test]
    public function isGetMatrixValid(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->generate('png');
        $matrix = $this->qrCodeGenerator->getMatrix();
        $this->assertInstanceOf(Matrix::class, $matrix);
    }

    /**
     * Test getting the matrix without generating the QR code
     */
    #[Test]
    public function isGetMatrixWithoutGenerate(): void
    {
        $this->expectException(\Error::class);
        $this->qrCodeGenerator->getMatrix();
    }

    /**
     * Test generating a valid QR code
     */
    #[Test]
    public function isGenerateValid(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->generate('png');
        $this->assertNotEmpty($this->qrCodeGenerator->getDataUri());
    }

    /**
     * Test generating a QR code with an invalid format
     */
    #[Test]
    public function isGenerateInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->generate('invalid-format');
    }

    /**
     * Test saving the QR code to a valid path
     */
    #[Test]
    public function isSaveToValidPath(): void
    {
        $outputPath = './test_qrcode';
        $outputFormat = 'png';
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->generate($outputFormat);
        $this->assertTrue($this->qrCodeGenerator->saveTo($outputPath));
        $this->assertFileExists($outputPath . '.' . $outputFormat);
        unlink($outputPath . '.' . $outputFormat);
    }

    /**
     * Data provider valid error correction levels
     */
    public static function validLevelsProvider(): array
    {
        return [
            ['Low', 'low'],
            ['medium', 'medium'],
            ['Quartile', 'quartile'],
            ['high', 'high'],
        ];
    }

    /**
     * Test setting valid error correction levels (case-insensitive)
     */
    #[Test]
    #[DataProvider('validLevelsProvider')]
    public function isSetValidErrorCorrectionLevel(string $level, string $expected): void
    {
        $this->qrCodeGenerator->setErrorCorrectionLevel($level);
        $this->assertSame($expected, $this->qrCodeGenerator->getErrorCorrectionLevel()->value);
    }

    /**
     * Data provider Invalid error correction levels
     */
    public static function invalidLevelsProvider(): array
    {
        return [['L'], ['M'], ['Q'], ['H'], ['invalid']];
    }

    /**
     * Test setting invalid error correction levels throws exception
     */
    #[Test]
    #[DataProvider('invalidLevelsProvider')]
    public function isSetInvalidErrorCorrectionLevel(string $level): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid error correction level. Accepted values: high, low, medium, quartile.');

        $this->qrCodeGenerator->setErrorCorrectionLevel($level);
    }

    /**
     * Data provider valid block size modes
     */
    public static function validBlockSizeModeProvider(): array
    {
        return [
            ['enlarge', 'enlarge'],
            ['Margin', 'margin'],
            ['nOne', 'none'],
            ['Shrink', 'shrink'],
        ];
    }

    /**
     * Test setting valid block size modes (case-insensitive)
     */
    #[Test]
    #[DataProvider('validBlockSizeModeProvider')]
    public function isSetValidBlockSizeMode(string $mode, string $expected): void
    {
        $this->qrCodeGenerator->setBlockSizeMode($mode);
        $this->assertSame($expected, $this->qrCodeGenerator->getBlockSizeMode()->value);
    }

    /**
     * Data Provider invalid block size modes
     */
    public static function invalidBlockSizeModeProvider(): array
    {
        return [['E'], ['M'], ['N'], ['S']];
    }

    /**
     * Test setting invalid block size modes throws exception
     */
    #[Test]
    #[DataProvider('invalidBlockSizeModeProvider')]
    public function isSetInvalidBlockSizeMode(string $mode): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid round block size mode. Accepted values: enlarge, margin, none, shrink.');
        $this->qrCodeGenerator->setBlockSizeMode($mode);
    }
}
