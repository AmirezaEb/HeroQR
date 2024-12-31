<?php

namespace Tests\HeroQR\Integration;

use PHPUnit\Framework\Attributes\Test;
use HeroQR\Core\QRCodeGenerator;
use PHPUnit\Framework\TestCase;
use HeroQR\DataTypes\DataType;
use InvalidArgumentException;

/**
 * Class StyledQRCodeTest
 * Tests the customization and styling functionality of QRCodeGenerator
 */
class StyledQRCodeTest extends TestCase
{
    private QRCodeGenerator $qrCodeGenerator;
    private string $outputPath;

    protected function setUp(): void
    {
        $this->qrCodeGenerator = new QRCodeGenerator();
        $this->outputPath = './testStyledQrcode-' . uniqid();
    }

    /**
     * Test generating a QR code with custom colors
     */
    #[Test]
    public function isQrcodeWithCustomColors(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.png');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.png'));

        unlink($this->outputPath . '.png');
    }

    /**
     * Test generating a QR code with default styling
     */
    #[Test]
    public function isQrcodeWithDefaultStyling(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);

        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.png');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.png'));

        unlink($this->outputPath . '.png');
    }

    /**
     * Test generating a QR code with invalid color codes
     */
    #[Test]
    public function isQrcodeWithInvalidColors(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('INVALID_COLOR');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('png');
    }

    /**
     * Test generating a QR code with custom size and margin
     */
    #[Test]
    public function isQrcodeWithCustomSizeAndMargin(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(500);
        $this->qrCodeGenerator->setMargin(50);
        $this->qrCodeGenerator->setColor('#000000');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.png');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.png'));

        unlink($this->outputPath . '.png');
    }

    /**
     * Test generating a QR code with invalid size
     */
    #[Test]
    public function isQrcodeWithInvalidSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(-100);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#000000');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('png');
    }
}
