<?php

namespace HeroQR\Tests\Unit\Core\Writers;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use HeroQR\Core\QRCodeGenerator;
use Endroid\QrCode\Matrix\Matrix;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class CustomPngWriterTest
 * Tests the CustomPngWriter class
 */
class CustomPngWriterTest extends TestCase
{
    private $qrCodeGenerator;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        $this->qrCodeGenerator = new QRCodeGenerator();
    }

    /**
     * Set up the QRCodeGenerator instance before each test
     */
    #[Test]
    public function isGetMatrixValid(): void
    {
        for ($i = 0; $i <= 9; $i++) {
            $markerNum = mt_rand(1, 3);
            $cursorNum = mt_rand(1, 3);
            $this->qrCodeGenerator->setSize(100)
                ->generate("png-M$markerNum-C$cursorNum");
            $matrix = $this->qrCodeGenerator->getMatrix();
            $this->assertInstanceOf(Matrix::class, $matrix);
        }
    }

    /**
     * Test generating QR code matrix validity
     */
    #[Test]
    public function isInvalidMarkerAndCursor(): void
    {
        for ($i = 0; $i <= 2; $i++) {
            $markerNum = mt_rand(4, 10);
            $cursorNum = mt_rand(4, 10);
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('Invalid Custom Marker Or Custom Cursor');

            $this->qrCodeGenerator->setSize(100)
                ->generate("png-M$markerNum-C$cursorNum");
        }
    }

    /**
     * Test invalid custom QR code format
     */
    #[Test]
    public function isInvalidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Custom Format "svg-M1-C2" Does Not Exist');

        $this->qrCodeGenerator->setSize(100)
            ->generate("svg-M1-C2");
    }

    /**
     * Test generating QR code with logo and label
     */
    #[Test]
    public function isWriteWithLogoAndLabel(): void
    {
        $logoPath = __DIR__ . '/testLogo.png';

        $image = imagecreatetruecolor(100, 100);
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $backgroundColor);
        imagepng($image, $logoPath);
        imagedestroy($image);

        $this->qrCodeGenerator->setLogo($logoPath, 50);
        $this->qrCodeGenerator->setLabel('Test Label', 'center', '#000000', 20, [0, 10, 10, 10]);

        $result = $this->qrCodeGenerator->generate('png-M1-C1')->getDataUri();

        unlink($logoPath);
        $this->assertStringStartsWith('data:image/png;base64', $result, 'Data URI Should Start With The Correct Prefix');
        $this->assertNotEmpty($result);
    }
}
