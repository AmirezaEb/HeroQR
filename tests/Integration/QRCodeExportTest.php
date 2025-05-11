<?php

namespace HeroQR\Tests\Integration;

use HeroQR\{Core\QRCodeGenerator, DataTypes\DataType};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

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
     * Provides supported export formats
     */
    public static function formatProvider(): array
    {
        return [['png'], ['svg'], ['webp'], ['gif'], ['eps'], ['binary']];
    }

    /**
     * Test exporting QR code to multiple formats using a data provider
     */
    #[Test]
    #[DataProvider('formatProvider')]
    public function itExportsQrcodeToSupportedFormats(string $format): void
    {
        $this->prepareQRCodeGenerator();

        $this->qrCodeGenerator->generate($format);
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $file = $this->outputPath . '.' . ($format === 'binary' ? 'bin' : $format);

        $this->assertFileExists($file);
        $this->assertNotEmpty(file_get_contents($file));

        $this->deleteFile($file);
    }

    /**
     * Test exporting to PDF format conditionally
     */
    #[Test]
    public function itExportsQrcodeToPdfIfAvailable(): void
    {
        $this->prepareQRCodeGenerator();

        if (class_exists('FPDF')) {
            $this->qrCodeGenerator->generate('pdf');
            $this->qrCodeGenerator->saveTo($this->outputPath);

            $file = $this->outputPath . '.pdf';

            $this->assertFileExists($file);
            $this->assertNotEmpty(file_get_contents($file));

            $this->deleteFile($file);
        } else {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Unable to find FPDF: check your installation');
            $this->qrCodeGenerator->generate('pdf');
        }
    }

    /**
     * Test exporting with invalid format
     */
    #[Test]
    public function itFailsExportingWithInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->prepareQRCodeGenerator();
        $this->qrCodeGenerator->generate('invalid-format');
    }

    /**
     * Test export without calling generate() first
     */
    #[Test]
    public function itFailsExportingWithoutGenerateCall(): void
    {
        $this->expectException(\Error::class);
        $this->qrCodeGenerator->saveTo($this->outputPath);
    }

    /**
     * Set up common QR code generator settings
     */
    private function prepareQRCodeGenerator(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');
    }

    /**
     * Delete generated file
     */
    private function deleteFile(string $file): void
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }
}