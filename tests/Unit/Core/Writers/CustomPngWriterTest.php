<?php

namespace HeroQR\Tests\Unit\Core\Writers;

use HeroQR\Core\QRCodeGenerator;
use Endroid\QrCode\Matrix\Matrix;
use PHPUnit\Framework\{TestCase,Attributes\Test};

/**
 * Class CustomPngWriterTest
 * Tests the CustomPngWriter class
 */
class CustomPngWriterTest extends TestCase
{
    private QRCodeGenerator $qrCodeGenerator;

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
        $matrix = $this->qrCodeGenerator->setSize(250)
            ->generate('png', [
                'Shape' => "S2",
                'Marker' => "M3",
                'Cursor' => "C4",
            ])->getMatrix();

        $this->assertIsObject($matrix);
        $this->assertInstanceOf(Matrix::class, $matrix);
    }

    /**
     * Test generating QR code matrix validity
     */
    #[Test]
    public function isInvalidMarkerAndCursor(): void
    {
        for ($i = 0; $i <= 2; $i++) {

            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessageMatches('/Invalid key \'.+\' provided. Valid keys are : .+/');

            $this->qrCodeGenerator->setSize(100)
                ->generate('png', [
                    'Shape' => 'S5',
                    'Marker' => 'M5',
                    'Cursor' => 'C5'
                ]);
        }
    }

    /**
     * Test invalid custom QR code format
     */
    #[Test]
    public function isInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom writers not supported for 'svg'");

        $this->qrCodeGenerator->setSize(100)
            ->generate('svg', [
                'Shape' => 'S' . random_int(1, 4),
                'Marker' => 'M' . random_int(1, 4),
                'Cursor' => 'C' . random_int(1, 4),
            ]);
    }

    /**
     * Test generating QR code with logo and label
     */
    #[Test]
    public function isWriteWithLogoAndLabel(): void
    {
        $tempDir = sys_get_temp_dir();
        $logoPath = $tempDir . DIRECTORY_SEPARATOR . 'testLogo_' . uniqid() . '.png';

        $image = @imagecreatetruecolor(100, 100);
        if (!$image) {
            $this->fail('Unable to create image resource.');
        }

        $backgroundColor = @imagecolorallocate($image, 255, 255, 255);
        if ($backgroundColor === false) {
            $this->fail('Unable to allocate background color.');
        }

        @imagefill($image, 0, 0, $backgroundColor);

        $pngResult = @imagepng($image, $logoPath);
        if ($pngResult === false) {
            $this->fail('Unable to save PNG image.');
        }

        imagedestroy($image);

        if (!file_exists($logoPath) || !is_readable($logoPath)) {
            $this->fail('Logo path is not accessible or not readable.');
        }

        $this->qrCodeGenerator->setLogo($logoPath, 50);
        $this->qrCodeGenerator->setLabel('Test Label', 'center', '#000000', 20, [0, 10, 10, 10]);

        $result = $this->qrCodeGenerator->generate('png', [
            'Shape' => "S2",
            'Marker' => "M2",
            'Cursor' => "C3",
        ])->getDataUri();

        $this->assertStringStartsWith('data:image/png;base64', $result, 'Data URI should start with the correct prefix.');
        $this->assertNotEmpty($result, 'Generated QR code data should not be empty.');
        $this->assertIsString($result, 'Generated QR code data should be a string.');

        if (file_exists($logoPath)) {
            unlink($logoPath);
        }
    }
}
