<?php

namespace HeroQR\Tests\Integration;

use HeroQR\{Core\QRCodeGenerator, DataTypes\DataType};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class QRCodeOptionsTest
 * Integration tests for combined QR options such as encoding, ECL and block size.
 */
class QRCodeOptionsTest extends TestCase
{
    private QRCodeGenerator $qrCodeGenerator;
    private string $outputPath;

    /**
     * Initializes the QRCodeGenerator instance
     */
    protected function setUp(): void
    {
        $this->qrCodeGenerator = new QRCodeGenerator();
        $this->outputPath = './testOptionsQrcode-' . uniqid();
    }

    /**
     * Provides error correction levels
     */
    public static function errorCorrectionProvider(): array
    {
        return [
            'low' => ['low'],
            'medium' => ['medium'],
            'quartile' => ['quartile'],
            'high' => ['high'],
        ];
    }

    /**
     * Test exporting QR codes with each error correction level
     */
    #[Test]
    #[DataProvider('errorCorrectionProvider')]
    public function isExportsWithErrorCorrectionLevels(string $level): void
    {
        $this->qrCodeGenerator
            ->setData('https://example.com', DataType::Url)
            ->setErrorCorrectionLevel($level)
            ->generate('png')
            ->saveTo($this->outputPath);

        $file = $this->outputPath . '.png';
        $this->assertFileExists($file);
        unlink($file);
    }

    /**
     * Provides block size modes
     */
    public static function blockSizeModeProvider(): array
    {
        return [
            'enlarge' => ['enlarge'],
            'margin' => ['margin'],
            'none' => ['none'],
            'shrink' => ['shrink'],
        ];
    }

    /**
     * Test exporting QR codes with each block size mode
     */
    #[Test]
    #[DataProvider('blockSizeModeProvider')]
    public function isExportsWithBlockSizeModes(string $mode): void
    {
        $this->qrCodeGenerator
            ->setData('Block Mode', DataType::Text)
            ->setBlockSizeMode($mode)
            ->generate('svg')
            ->saveTo($this->outputPath);

        $file = $this->outputPath . '.svg';
        $this->assertFileExists($file);
        unlink($file);
    }

    /**
     * Test generating with encoding and label together
     */
    #[Test]
    public function isGeneratesWithEncodingAndLabel(): void
    {
        $this->qrCodeGenerator
            ->setData('Encoded Label', DataType::Text)
            ->setEncoding('UTF-8')
            ->setLabel('Footer', 'right', ['r' => 50, 'g' => 50, 'b' => 50, 'a' => 1], 16, [0, 5, 5, 5])
            ->setSize(280)
            ->setMargin(8)
            ->generate('png')
            ->saveTo($this->outputPath);

        $file = $this->outputPath . '.png';
        $this->assertFileExists($file);
        $this->assertNotEmpty(file_get_contents($file));
        unlink($file);
    }

    /**
     * Test matrix array dimensions stay consistent after regeneration
     */
    #[Test]
    public function isMatrixConsistentAfterRegeneration(): void
    {
        $this->qrCodeGenerator->setData('Consistency', DataType::Text);

        $this->qrCodeGenerator->generate('png');
        $first = $this->qrCodeGenerator->getMatrixAsArray();

        $this->qrCodeGenerator->generate('png');
        $second = $this->qrCodeGenerator->getMatrixAsArray();

        $this->assertSame($first, $second);
    }

    /**
     * Test webp and gif exports with custom colors
     */
    #[Test]
    public function isExportsWebpAndGifWithColors(): void
    {
        foreach (['webp', 'gif'] as $format) {
            $path = $this->outputPath . '-' . $format;

            $this->qrCodeGenerator
                ->setData('https://example.org', DataType::Url)
                ->setColor(80, 20, 120, 1)
                ->setBackgroundColor(255, 255, 255, 1)
                ->generate($format)
                ->saveTo($path);

            $file = $path . '.' . $format;
            $this->assertFileExists($file);
            unlink($file);
        }
    }
}
