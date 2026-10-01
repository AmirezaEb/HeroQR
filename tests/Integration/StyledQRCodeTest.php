<?php

namespace HeroQR\Tests\Integration;

use HeroQR\{Core\QRCodeGenerator, DataTypes\DataType};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class StyledQRCodeTest
 * Tests the customization and styling functionality of QRCodeGenerator
 */
class StyledQRCodeTest extends TestCase
{
    private const DEFAULT_DATA = 'https://heroqr.test';
    private const DEFAULT_DATATYPE = DataType::Url;
    private string $outputPath;
    private QRCodeGenerator $qrCodeGenerator;

    /**
     * Initializes the QRCodeGenerator instance
     */
    protected function setUp(): void
    {
        $this->outputPath = './testStyledQrcode-' . uniqid();
        $this->qrCodeGenerator = new QRCodeGenerator();
    }

    /**
     * Test generating QR code with valid custom options for both PNG and SVG
     */
    #[Test]
    public function isGeneratesFileWithValidCustomOptions(): void
    {
        $this->qrCodeGenerator->setData(self::DEFAULT_DATA, self::DEFAULT_DATATYPE);
        $this->configureQRCode(
            size: 500,
            margin: 50,
            color: ['r' => 225, 'g' => 225, 'b' => 225, 'a' => 0.4],
            bgColor: ['r' => 225, 'g' => 225, 'b' => 225, 'a' => 1.0]
        );

        foreach (['png', 'svg'] as $format) {
            $this->qrCodeGenerator->generate($format, [
                'Shape' => 'S2',
                'Cursor' => 'C3',
                'Marker' => 'M1'
            ]);

            $this->qrCodeGenerator->saveTo($this->outputPath);

            $file = $this->outputPath . '.' . $format;

            $this->assertFileExists($file, "Failed for format: $format");
            $this->assertNotEmpty(file_get_contents($file));

            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Test generating QR code with default settings for both formats
     */
    #[Test]
    public function isGeneratesFileWithDefaultSettings(): void
    {
        $this->qrCodeGenerator->setData(self::DEFAULT_DATA, self::DEFAULT_DATATYPE);
        $this->configureQRCode();

        foreach (['png', 'svg'] as $format) {
            $this->qrCodeGenerator->generate($format);
            $this->assertGeneratedFileExistsAndCleanup($format);
        }
    }

    /**
     * Test that an SVG generated with alpha channel contains the fill-opacity attribute
     */
    #[Test]
    public function isSvgHasOpacityAttribute(): void
    {
        $this->qrCodeGenerator->setData(self::DEFAULT_DATA, self::DEFAULT_DATATYPE);
        $this->qrCodeGenerator->setColor(255, 0, 0, 0.5);
        $this->qrCodeGenerator->generate('svg');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $file = $this->outputPath . '.svg';
        $content = file_get_contents($file);

        $this->assertStringContainsString('fill-opacity', $content);

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Test Throws exception on invalid color
     */
    #[Test]
    public function isThrowsExceptionOnInvalidColor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setColor(0, 200, 200, 1.1);
    }

    /**
     * Test Throws exception on invalid background color
     */
    #[Test]
    public function isThrowsExceptionOnInvalidBackgroundColor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setBackgroundColor(0, 200, 256, 1);
    }

    /**
     * Test Throws exception if save is called before generation
     */
    #[Test]
    public function isThrowsExceptionIfSaveCalledWithoutGeneration(): void
    {
        $this->expectException(\Error::class);
        $this->qrCodeGenerator->saveTo($this->outputPath);
    }

    /**
     * Provides invalid custom options
     */
    public static function invalidCustomizationProvider(): array
    {
        return [
            'invalid marker' => [['Marker' => 'M8']],
            'invalid shape' => [['Shape' => 'S5']],
            'invalid cursor' => [['Cursor' => 'C8']],
            'multiple invalid' => [['Shape' => 'S5', 'Cursor' => 'C8', 'Marker' => 'M8']],
        ];
    }

    /**
     * Test Throws exception on invalid customizations
     */
    #[Test]
    #[DataProvider('invalidCustomizationProvider')]
    public function isThrowsExceptionForInvalidCustomizations(array $options): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->qrCodeGenerator->setData(self::DEFAULT_DATA, self::DEFAULT_DATATYPE);
        $this->configureQRCode();
        $this->qrCodeGenerator->generate('png', $options);
    }

    /**
     * Configure size, margin, color, and background
     */
    private function configureQRCode(
        int   $size = 300,
        int   $margin = 10,
        array $color = ['r' => 225, 'g' => 225, 'b' => 225, 'a' => 1.0],
        array $bgColor = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 1.0]
    ): void
    {
        $this->qrCodeGenerator->setSize($size);
        $this->qrCodeGenerator->setMargin($margin);
        $this->qrCodeGenerator->setColor($color['r'], $color['g'], $color['b'], $color['a']);
        $this->qrCodeGenerator->setBackgroundColor($bgColor['r'], $bgColor['g'], $bgColor['b'], $bgColor['a']);
    }

    /**
     * Assert output file exists and remove it
     */
    private function assertGeneratedFileExistsAndCleanup(string $format): void
    {
        $file = $this->outputPath . '.' . $format;

        $this->qrCodeGenerator->saveTo($this->outputPath);
        $this->assertFileExists($file);
        $this->assertNotEmpty(file_get_contents($file));

        if (file_exists($file)) {
            unlink($file);
        }
    }
}