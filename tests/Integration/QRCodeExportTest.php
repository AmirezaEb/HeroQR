<?php

namespace HeroQR\Tests\Integration;

use Error;
use RuntimeException;
use InvalidArgumentException;
use HeroQR\DataTypes\DataType;
use PHPUnit\Framework\TestCase;
use HeroQR\Core\QRCodeGenerator;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class QRCodeExportTest
 * Tests the export functionality of QRCodeGenerator
 */
class QRCodeExportTest extends TestCase
{
    private QRCodeGenerator $qrCodeGenerator;
    private string $outputPath;

    protected function setUp(): void
    {
        $this->qrCodeGenerator = new QRCodeGenerator();
        $this->outputPath = './testQrcode-' . uniqid();
    }

    /**
     * Test exporting QR code to PNG format
     */
    #[Test]
    public function isExportQrcodeToPng(): void
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
     * Test exporting QR code to SVG format
     */
    #[Test]
    public function isExportQrcodeToSvg(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('svg');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.svg');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.svg'));

        unlink($this->outputPath . '.svg');
    }

    /**
     * Test exporting QR code to PDF format
     */
    #[Test]
    public function isExportQrcodeToPdf(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        if (class_exists('FPDF')) {
            $this->qrCodeGenerator->generate('pdf');
            $this->qrCodeGenerator->saveTo($this->outputPath);
            $this->assertFileExists($this->outputPath . '.pdf');
            $this->assertNotEmpty(file_get_contents($this->outputPath . '.pdf'));
            unlink($this->outputPath . '.pdf');
        } else {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('The library "<a href="https://github.com/Setasign/FPDF" target="_blank" style="text-decoration: none;">setasign/fpdf</a>" is required. Please install it using "composer require setasign/fpdf".');
            $this->qrCodeGenerator->generate('pdf');
        }
    }

    /**
     * Test exporting QR code to webp format
     */
    #[Test]
    public function isExportQrcodeToWebp(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('webp');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.webp');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.webp'));

        unlink($this->outputPath . '.webp');
    }

    /**
     * Test exporting QR code to Binary format
     */
    #[Test]
    public function isExportQrcodeToBin(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('binary');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.bin');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.bin'));

        unlink($this->outputPath . '.bin');
    }

    /**
     * Test exporting QR code to Gif format
     */
    #[Test]
    public function isExportQrcodeToGif(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('gif');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.gif');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.gif'));

        unlink($this->outputPath . '.gif');
    }

    /**
     * Test exporting QR code to Eps format
     */
    #[Test]
    public function isExportQrcodeToEps(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('eps');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.eps');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.eps'));

        unlink($this->outputPath . '.eps');
    }

    /**
     * Test invalid format for exporting QR code
     */
    #[Test]
    public function isExportQrcodeInvalidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $this->qrCodeGenerator->generate('invalid-format');
    }

    /**
     * Test export without calling generate() first
     */
    #[Test]
    public function isExportWithoutGenerate(): void
    {
        $this->expectException(Error::class);

        $this->qrCodeGenerator->saveTo($this->outputPath);
    }
}
