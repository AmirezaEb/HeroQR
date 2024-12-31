<?php

namespace HeroQR\Tests\Integration;

use PHPUnit\Framework\Attributes\Test;
use HeroQR\Core\QRCodeGenerator;
use PHPUnit\Framework\TestCase;
use HeroQR\DataTypes\DataType;
use InvalidArgumentException;

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
     * @return string The path to the generated logo image
     */
    private function createLogo(): string
    {
        $width = 100;
        $height = 100;
        $image = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $circleColor = imagecolorallocate($image, 255, 87, 51);

        imagefill($image, 0, 0, $bgColor);

        imagefilledellipse($image, $width / 2, $height / 2, $width - 20, $height - 20, $circleColor);

        $logoPath = './test_logo.png';
        imagepng($image, $logoPath);

        imagedestroy($image);

        return $logoPath;
    }

    /**
     * Test generating QR code with logo
     */
    #[Test]
    public function isQrcodeWithLogo(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $logoPath = $this->createLogo();
        $this->qrCodeGenerator->setLogo($logoPath);

        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $this->assertFileExists($this->outputPath . '.png');
        $this->assertNotEmpty(file_get_contents($this->outputPath . '.png'));

        unlink($logoPath);
        unlink($this->outputPath . '.png');
    }

    /**
     * Test invalid logo path
     */
    #[Test]
    public function isQrcodeWithInvalidLogo(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->setSize(300);
        $this->qrCodeGenerator->setMargin(20);
        $this->qrCodeGenerator->setColor('#FF5733');
        $this->qrCodeGenerator->setBackgroundColor('#FFFFFF');

        $invalidLogoPath = './path/to/nonexistent/logo.png';
        $this->qrCodeGenerator->setLogo($invalidLogoPath);

        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);
    }

    /**
     * Test export without logo
     */
    #[Test]
    public function isQrcodeWithoutLogo(): void
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
}
