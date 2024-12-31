<?php

namespace Tests\HeroQR\Integration;

use PHPUnit\Framework\Attributes\Test;
use HeroQR\Core\QRCodeGenerator;
use PHPUnit\Framework\TestCase;
use HeroQR\DataTypes\DataType;
use InvalidArgumentException;
use RuntimeException;
use Error;

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
            $this->expectExceptionMessage('<a href="https://github.com/Setasign/FPDF" target="_blank" style="text-decoration: none;">setasign/fpdf</a>" is required for PDF generation. Please install it using "composer require setasign/fpdf');
            $this->qrCodeGenerator->generate('pdf');
        }
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
