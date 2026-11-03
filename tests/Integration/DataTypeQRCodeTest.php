<?php

namespace HeroQR\Tests\Integration;

use HeroQR\{Core\QRCodeGenerator, DataTypes\DataType};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class DataTypeQRCodeTest
 * Integration tests for generating QR codes with different DataType values.
 */
class DataTypeQRCodeTest extends TestCase
{
    private QRCodeGenerator $qrCodeGenerator;
    private string $outputPath;

    /**
     * Initializes the QRCodeGenerator instance
     */
    protected function setUp(): void
    {
        $this->qrCodeGenerator = new QRCodeGenerator();
        $this->outputPath = './testDataTypeQrcode-' . uniqid();
    }

    /**
     * Provides valid data and DataType pairs for generation
     */
    public static function dataTypeProvider(): array
    {
        return [
            'Text' => ['HeroQR Integration', DataType::Text],
            'Url' => ['https://example.com/path', DataType::Url],
            'None' => ['raw-none-data', DataType::None],
            'Wifi' => ['WIFI:T:WPA2;S:HeroNet;P:secretpass;', DataType::Wifi],
            'Location' => ['35.6892,51.3890', DataType::Location],
        ];
    }

    /**
     * Test generating PNG QR codes for various data types
     */
    #[Test]
    #[DataProvider('dataTypeProvider')]
    public function isGeneratesPngForDataTypes(string $data, DataType $type): void
    {
        $this->qrCodeGenerator->setData($data, $type);
        $this->qrCodeGenerator->generate('png');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $file = $this->outputPath . '.png';
        $this->assertFileExists($file);
        $this->assertNotEmpty(file_get_contents($file));

        unlink($file);
    }

    /**
     * Test generating SVG QR codes for various data types
     */
    #[Test]
    #[DataProvider('dataTypeProvider')]
    public function isGeneratesSvgForDataTypes(string $data, DataType $type): void
    {
        $this->qrCodeGenerator->setData($data, $type);
        $this->qrCodeGenerator->generate('svg');
        $this->qrCodeGenerator->saveTo($this->outputPath);

        $file = $this->outputPath . '.svg';
        $this->assertFileExists($file);
        $this->assertStringContainsString('<svg', file_get_contents($file));

        unlink($file);
    }

    /**
     * Test email type generates a mailto QR and exports successfully
     */
    #[Test]
    public function isGeneratesEmailMailtoQrcode(): void
    {
        $this->qrCodeGenerator->setData('aabrahimi1718@gmail.com', DataType::Email);
        $this->assertStringStartsWith('mailto:', $this->qrCodeGenerator->getData());

        $this->qrCodeGenerator->generate('png');
        $this->assertStringStartsWith('data:image/png;base64', $this->qrCodeGenerator->getDataUri());
    }

    /**
     * Test phone type when libphonenumber is available
     */
    #[Test]
    public function isGeneratesPhoneQrcodeWhenLibraryAvailable(): void
    {
        if (!class_exists(\libphonenumber\PhoneNumberUtil::class)) {
            $this->expectException(\RuntimeException::class);
            $this->qrCodeGenerator->setData('+989358919279', DataType::Phone);
            return;
        }

        $this->qrCodeGenerator->setData('+989358919279', DataType::Phone);
        $this->assertStringStartsWith('tel:', $this->qrCodeGenerator->getData());

        $this->qrCodeGenerator->generate('svg');
        $this->assertStringStartsWith('data:image/svg+xml;base64', $this->qrCodeGenerator->getDataUri());
    }

    /**
     * Test styled generation with all shape variants
     */
    public static function shapeProvider(): array
    {
        return [
            'S1' => ['S1'],
            'S2' => ['S2'],
            'S3' => ['S3'],
            'S4' => ['S4'],
        ];
    }

    /**
     * Test custom PNG generation for each shape
     */
    #[Test]
    #[DataProvider('shapeProvider')]
    public function isGeneratesCustomPngForEachShape(string $shape): void
    {
        $this->qrCodeGenerator
            ->setData('https://heroqr.test', DataType::Url)
            ->setSize(200)
            ->generate('png', [
                'Shape' => $shape,
                'Marker' => 'M1',
                'Cursor' => 'C1',
            ])
            ->saveTo($this->outputPath);

        $file = $this->outputPath . '.png';
        $this->assertFileExists($file);
        unlink($file);
    }

    /**
     * Test custom SVG generation for each shape
     */
    #[Test]
    #[DataProvider('shapeProvider')]
    public function isGeneratesCustomSvgForEachShape(string $shape): void
    {
        $this->qrCodeGenerator
            ->setData('Styled Shape', DataType::Text)
            ->setSize(200)
            ->generate('svg', [
                'Shape' => $shape,
                'Marker' => 'M2',
                'Cursor' => 'C3',
            ])
            ->saveTo($this->outputPath);

        $file = $this->outputPath . '.svg';
        $this->assertFileExists($file);
        $this->assertStringContainsString('<svg', file_get_contents($file));
        unlink($file);
    }
}
