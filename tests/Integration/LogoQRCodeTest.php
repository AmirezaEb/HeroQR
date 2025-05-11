<?php

namespace HeroQR\Tests\Integration;

use PHPUnit\Framework\{Attributes\Test,TestCase};
use HeroQR\{Core\QRCodeGenerator,DataTypes\DataType};

/**
 * Class LogoQRCodeTest
 * Tests the logo functionality of QRCodeGenerator
 */
class LogoQRCodeTest extends TestCase
{
    private QRCodeGenerator $qrCodeGenerator;
    private string $outputPath;

    protected function setUp(): void
    {
        $this->qrCodeGenerator = new QRCodeGenerator();
        $this->outputPath = './testQrcodeLogo-' . uniqid();
    }

    /**
     * Create a simple logo image in memory for testing
     */
    private function createLogo(): string
    {
        $image = imagecreatetruecolor(100, 100);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $circleColor = imagecolorallocate($image, 255, 87, 51);

        imagefill($image, 0, 0, $bgColor);
        imagefilledellipse($image, 50, 50, 80, 80, $circleColor);

        $logoPath = './test_logo.png';
        imagepng($image, $logoPath);
        imagedestroy($image);

        return $logoPath;
    }

    /**
     * Test generating QR code with logo
     */
    #[Test]
    public function itGeneratesQrcodeWithLogo(): void
    {
        $logoPath = $this->createLogo();
        $this->configureQrCodeGenerator($logoPath);

        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.png');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.png'));

        $this->cleanUp([$logoPath, $this->outputPath . '.png']);
    }

    /**
     * Test invalid logo path
     */
    #[Test]
    public function itFailsWithInvalidLogoPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->configureQrCodeGenerator('./path/to/nonexistent/logo.png');
        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);
    }

    /**
     * Test export without logo
     */
    #[Test]
    public function itGeneratesQrcodeWithoutLogo(): void
    {
        $this->configureQrCodeGenerator();

        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.png');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.png'));

        unlink($this->outputPath . '.png');
    }

    /**
     * Helper method to configure QRCodeGenerator with common settings.
     */
    private function configureQrCodeGenerator(string $logoPath = ''): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        if ($logoPath) {
            $this->qrCodeGenerator->setLogo($logoPath);
        }
    }

    /**
     * Helper method to clean up generated files.
     */
    private function cleanUp(array $files): void
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
