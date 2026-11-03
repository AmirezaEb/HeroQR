<?php

namespace HeroQR\Tests\Unit\Core;

use HeroQR\{Core\QRCodeGenerator, DataTypes\DataType};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class QRCodeGeneratorExtendedTest
 * Extended coverage for QRCodeGenerator defaults, sanitization, chaining and outputs.
 */
class QRCodeGeneratorExtendedTest extends TestCase
{
    private QRCodeGenerator $qrCodeGenerator;

    /**
     * Set up the QRCodeGenerator instance before each test
     */
    protected function setUp(): void
    {
        $this->qrCodeGenerator = new QRCodeGenerator();
    }

    /**
     * Test default size, margin and data values
     */
    #[Test]
    public function isDefaultPropertiesCorrect(): void
    {
        $this->assertSame(800, $this->qrCodeGenerator->getSize());
        $this->assertSame(10, $this->qrCodeGenerator->getMargin());
        $this->assertSame('https://github.com/AmirezaEb/HeroQR', $this->qrCodeGenerator->getData());
        $this->assertSame('high', $this->qrCodeGenerator->getErrorCorrectionLevel()->value);
        $this->assertSame('margin', $this->qrCodeGenerator->getBlockSizeMode()->value);
    }

    /**
     * Test that empty data throws an exception
     */
    #[Test]
    public function isSetDataEmptyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Data cannot be empty.');

        $this->qrCodeGenerator->setData('   ', DataType::None);
    }

    /**
     * Test setting data with DataType::None
     */
    #[Test]
    public function isSetDataWithNoneType(): void
    {
        $this->qrCodeGenerator->setData('any content', DataType::None);
        $this->assertSame('any content', $this->qrCodeGenerator->getData());
    }

    /**
     * Test that email data is sanitized with mailto prefix
     */
    #[Test]
    public function isSetDataEmailIsSanitized(): void
    {
        $this->qrCodeGenerator->setData('aabrahimi1718@gmail.com', DataType::Email);
        $this->assertSame('mailto:aabrahimi1718@gmail.com', $this->qrCodeGenerator->getData());
    }

    /**
     * Test that invalid email data throws an exception
     */
    #[Test]
    public function isSetDataInvalidEmailThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setData('not-an-email', DataType::Email);
    }

    /**
     * Test that location data is sanitized to a Google Maps URL
     */
    #[Test]
    public function isSetDataLocationIsSanitized(): void
    {
        $this->qrCodeGenerator->setData('35.6892,51.3890', DataType::Location);
        $this->assertSame('https://www.google.com/maps?q=35.6892,51.3890', $this->qrCodeGenerator->getData());
    }

    /**
     * Test that invalid location data throws an exception
     */
    #[Test]
    public function isSetDataInvalidLocationThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setData('not-a-location', DataType::Location);
    }

    /**
     * Test that wifi data is stored as provided
     */
    #[Test]
    public function isSetDataWifiIsStored(): void
    {
        $wifi = 'WIFI:T:WPA2;S:MyNetwork;P:password123;';
        $this->qrCodeGenerator->setData($wifi, DataType::Wifi);
        $this->assertSame($wifi, $this->qrCodeGenerator->getData());
    }

    /**
     * Test that invalid wifi data throws an exception
     */
    #[Test]
    public function isSetDataInvalidWifiThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setData('WIFI:invalid', DataType::Wifi);
    }

    /**
     * Test that text data validation rejects unsafe content
     */
    #[Test]
    public function isSetDataInvalidTextThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->qrCodeGenerator->setData("<script>alert('x')</script>", DataType::Text);
    }

    /**
     * Test that size zero is rejected
     */
    #[Test]
    public function isSetSizeZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Size must be a positive integer.');
        $this->qrCodeGenerator->setSize(0);
    }

    /**
     * Test that margin zero is accepted
     */
    #[Test]
    public function isSetMarginZeroValid(): void
    {
        $this->qrCodeGenerator->setMargin(0);
        $this->assertSame(0, $this->qrCodeGenerator->getMargin());
    }

    /**
     * Test method chaining returns the same instance
     */
    #[Test]
    public function isMethodChainingReturnsSelf(): void
    {
        $result = $this->qrCodeGenerator
            ->setData('HeroQR', DataType::Text)
            ->setSize(250)
            ->setMargin(5)
            ->setColor(10, 20, 30)
            ->setBackgroundColor(255, 255, 255)
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel('medium')
            ->setBlockSizeMode('none');

        $this->assertSame($this->qrCodeGenerator, $result);
        $this->assertSame(250, $result->getSize());
        $this->assertSame(5, $result->getMargin());
        $this->assertSame('medium', $result->getErrorCorrectionLevel()->value);
        $this->assertSame('none', $result->getBlockSizeMode()->value);
    }

    /**
     * Test empty label throws an exception
     */
    #[Test]
    public function isSetLabelEmptyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Label cannot be empty');
        $this->qrCodeGenerator->setLabel('');
    }

    /**
     * Test getMatrixAsArray after generation
     */
    #[Test]
    public function isGetMatrixAsArrayValid(): void
    {
        $this->qrCodeGenerator->setData('https://example.com', DataType::Url);
        $this->qrCodeGenerator->generate('png');

        $matrix = $this->qrCodeGenerator->getMatrixAsArray();

        $this->assertIsArray($matrix);
        $this->assertNotEmpty($matrix);
        $this->assertIsArray($matrix[0]);
        $this->assertContainsOnly('int', $matrix[0]);
    }

    /**
     * Test getMatrixAsArray without generation throws Error
     */
    #[Test]
    public function isGetMatrixAsArrayWithoutGenerate(): void
    {
        $this->expectException(\Error::class);
        $this->qrCodeGenerator->getMatrixAsArray();
    }

    /**
     * Test getString returns raw PNG binary content
     */
    #[Test]
    public function isGetStringValidPng(): void
    {
        $this->qrCodeGenerator->setData('HeroQR', DataType::Text);
        $this->qrCodeGenerator->generate('png');

        $string = $this->qrCodeGenerator->getString();

        $this->assertIsString($string);
        $this->assertNotEmpty($string);
        $this->assertStringStartsWith("\x89PNG", $string);
    }

    /**
     * Test getString without generation throws Error
     */
    #[Test]
    public function isGetStringWithoutGenerate(): void
    {
        $this->expectException(\Error::class);
        $this->qrCodeGenerator->getString();
    }

    /**
     * Test getDataUri without generation throws Error
     */
    #[Test]
    public function isGetDataUriWithoutGenerate(): void
    {
        $this->expectException(\Error::class);
        $this->qrCodeGenerator->getDataUri();
    }

    /**
     * Test __toString returns a data URI after generation
     */
    #[Test]
    public function isToStringReturnsDataUri(): void
    {
        $this->qrCodeGenerator->setData('HeroQR', DataType::Text);
        $this->qrCodeGenerator->generate('png');

        $asString = (string) $this->qrCodeGenerator;

        $this->assertStringStartsWith('data:image/png;base64', $asString);
    }

    /**
     * Test setting encoding through QRCodeGenerator
     */
    #[Test]
    public function isSetEncodingValid(): void
    {
        $result = $this->qrCodeGenerator->setEncoding('ISO-8859-1');
        $this->assertSame($this->qrCodeGenerator, $result);

        $this->qrCodeGenerator->setData('Hello', DataType::Text);
        $this->qrCodeGenerator->generate('png');
        $this->assertNotEmpty($this->qrCodeGenerator->getDataUri());
    }

    /**
     * Test logo path defaults to empty before setLogo
     */
    #[Test]
    public function isLogoPathEmptyByDefault(): void
    {
        $this->assertSame('', $this->qrCodeGenerator->getLogoPath());
    }

    /**
     * Test label defaults to empty before setLabel
     */
    #[Test]
    public function isLabelEmptyByDefault(): void
    {
        $this->assertSame('', $this->qrCodeGenerator->getLabel());
    }

    /**
     * Provides valid generate formats that do not require optional packages
     */
    public static function generateFormatProvider(): array
    {
        return [
            'png' => ['png', 'data:image/png;base64'],
            'svg' => ['svg', 'data:image/svg+xml;base64'],
            'gif' => ['gif', 'data:image/gif;base64'],
            'webp' => ['webp', 'data:image/webp;base64'],
        ];
    }

    /**
     * Test generating QR codes in multiple formats returns correct data URI prefixes
     */
    #[Test]
    #[DataProvider('generateFormatProvider')]
    public function isGenerateMultipleFormats(string $format, string $prefix): void
    {
        $this->qrCodeGenerator->setData('https://heroqr.test', DataType::Url);
        $this->qrCodeGenerator->generate($format);

        $uri = $this->qrCodeGenerator->getDataUri();
        $this->assertStringStartsWith($prefix, $uri);
    }

    /**
     * Test generating with label and custom colors produces output
     */
    #[Test]
    public function isGenerateWithLabelAndColors(): void
    {
        $this->qrCodeGenerator
            ->setData('Labeled QR', DataType::Text)
            ->setColor(30, 40, 50, 0.9)
            ->setBackgroundColor(240, 240, 240)
            ->setLabel('HeroQR Label', 'left', ['r' => 10, 'g' => 20, 'b' => 30, 'a' => 1], 18, [5, 5, 5, 5])
            ->generate('png');

        $this->assertSame('HeroQR Label', $this->qrCodeGenerator->getLabel());
        $this->assertStringStartsWith('data:image/png;base64', $this->qrCodeGenerator->getDataUri());
    }

    /**
     * Test generate returns self for chaining into saveTo
     */
    #[Test]
    public function isGenerateReturnsSelfForSaveTo(): void
    {
        $outputPath = './test_qrcode_chain_' . uniqid();

        $saved = $this->qrCodeGenerator
            ->setData('Chain Test', DataType::Text)
            ->generate('svg')
            ->saveTo($outputPath);

        $file = $outputPath . '.svg';
        $this->assertTrue($saved);
        $this->assertFileExists($file);

        unlink($file);
    }

    /**
     * Test background color with null alpha defaults to opaque
     */
    #[Test]
    public function isSetBackgroundColorNullAlphaDefaultsToOne(): void
    {
        $this->qrCodeGenerator->setBackgroundColor(10, 20, 30, null);
        $color = $this->qrCodeGenerator->getBackgroundColor();

        $this->assertEquals([10, 20, 30, 1], [
            $color->getRed(),
            $color->getGreen(),
            $color->getBlue(),
            $color->getOpacity(),
        ]);
    }
}
