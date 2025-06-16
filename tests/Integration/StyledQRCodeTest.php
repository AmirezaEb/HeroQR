<?php

namespace HeroQR\Tests\Integration;

use HeroQR\{Core\QRCodeGenerator,DataTypes\DataType};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class StyledQRCodeTest
 * Tests the customization and styling functionality of QRCodeGenerator
 */
class StyledQRCodeTest extends TestCase
{
    private const DEFAULT_DATA = 'https://heroqr.test';
    private const DEFAULT_DATATYPE = DataType::Url;
    private const DEFAULT_OUTPUT = 'png';
    private string $outputPath;
    private QRCodeGenerator $qrCodeGenerator;


    protected function setUp(): void
    {
        $this->outputPath = './testStyledQrcode-' . uniqid();
        $this->qrCodeGenerator = new QRCodeGenerator();
    }

    /**
     * Generates file with custom options
     */
    #[Test]
    public function itGeneratesFileWithValidCustomOptions(): void
    {
        $this->qrCodeGenerator->setData(self::DEFAULT_DATA, self::DEFAULT_DATATYPE);
        $this->configureQRCode(500, 50, '#FF5733', '#EEEEEE');
        $this->qrCodeGenerator->generate(self::DEFAULT_OUTPUT, [
            'Shape' => 'S2',
            'Cursor' => 'C3',
            'Marker' => 'M1'
        ]);

        $this->assertGeneratedFileExistsAndCleanup();
    }

    /**
     * Generates file with default settings
     */
    #[Test]
    public function itGeneratesFileWithDefaultSettings(): void
    {
        $this->qrCodeGenerator->setData(self::DEFAULT_DATA, self::DEFAULT_DATATYPE);
        $this->configureQRCode();
        $this->qrCodeGenerator->generate(self::DEFAULT_OUTPUT);

        $this->assertGeneratedFileExistsAndCleanup();
    }

    /**
     * Throws exception on invalid color
     */
    #[Test]
    public function itThrowsExceptionOnInvalidColor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setColor('red');
    }

    /**
     * Throws exception on invalid background color
     */
    #[Test]
    public function itThrowsExceptionOnInvalidBackgroundColor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setBackgroundColor('white');
    }

    /**
     * Throws exception if save is called before generation
     */
    #[Test]
    public function itThrowsExceptionIfSaveCalledWithoutGeneration(): void
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
            'invalid marker' => [['Marker' => 'M9']],
            'invalid shape' => [['Shape' => 'S9']],
            'invalid cursor' => [['Cursor' => 'C9']],
            'multiple invalid' => [['Shape' => 'S9', 'Cursor' => 'C9', 'Marker' => 'M5']],
        ];
    }

    /**
     * Throws exception on invalid customizations
     */
    #[Test]
    #[DataProvider('invalidCustomizationProvider')]
    public function itThrowsExceptionForInvalidCustomizations(array $options): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->qrCodeGenerator->setData(self::DEFAULT_DATA, self::DEFAULT_DATATYPE);
        $this->configureQRCode();
        $this->qrCodeGenerator->generate(self::DEFAULT_OUTPUT, $options);
    }

    /**
     * Configure size, margin, color, and background
     */
    private function configureQRCode(
        int $size = 300,
        int $margin = 10,
        string $color = '#000000',
        string $bgColor = '#FFFFFF'
    ): void {
        $this->qrCodeGenerator->setSize($size);
        $this->qrCodeGenerator->setMargin($margin);
        $this->qrCodeGenerator->setColor($color);
        $this->qrCodeGenerator->setBackgroundColor($bgColor);
    }

    /**
     * Assert output file exists and remove it
     */
    private function assertGeneratedFileExistsAndCleanup(): void
    {
        $file = $this->outputPath . '.' . self::DEFAULT_OUTPUT;

        $this->qrCodeGenerator->saveTo($this->outputPath);
        $this->assertFileExists($file);
        $this->assertNotEmpty(file_get_contents($file));

        if (file_exists($file)) {
            unlink($file);
        }
    }
}