<?php

namespace HeroQR\Tests\Unit\Managers;

use Endroid\QrCode\Writer\{GifWriter, PngWriter, SvgWriter};
use HeroQR\{Core\Writers\CustomPngWriter, Core\Writers\CustomSvgWriter, Managers\WriterManager};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class WriterManagerCustomsTest
 * Extended coverage for WriterManager custom patterns and format handling.
 */
class WriterManagerCustomsTest extends TestCase
{
    private WriterManager $writerManager;

    /**
     * Initializes the WriterManager instance
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->writerManager = new WriterManager();
    }

    /**
     * Test format names are case-insensitive
     */
    #[Test]
    public function isGetWriterCaseInsensitive(): void
    {
        $this->assertInstanceOf(PngWriter::class, $this->writerManager->getWriter('PNG'));
        $this->assertInstanceOf(SvgWriter::class, $this->writerManager->getWriter('Svg'));
        $this->assertInstanceOf(GifWriter::class, $this->writerManager->getWriter(' GiF '));
    }

    /**
     * Provides valid custom shape/marker/cursor combinations for PNG
     */
    public static function customPngProvider(): array
    {
        return [
            'S1 M1 C1' => ['S1', 'M1', 'C1'],
            'S2 M3 C4' => ['S2', 'M3', 'C4'],
            'S3 M5 C6' => ['S3', 'M5', 'C6'],
            'S4 M7 C7' => ['S4', 'M7', 'C7'],
            'lowercase customs' => ['s2', 'm2', 'c2'],
        ];
    }

    /**
     * Test custom PNG writers with various valid patterns
     */
    #[Test]
    #[DataProvider('customPngProvider')]
    public function isGetCustomPngWriter(string $shape, string $marker, string $cursor): void
    {
        $writer = $this->writerManager->getWriter('png', [
            'Shape' => $shape,
            'Marker' => $marker,
            'Cursor' => $cursor,
        ]);

        $this->assertInstanceOf(CustomPngWriter::class, $writer);
    }

    /**
     * Test custom SVG writers with various valid patterns
     */
    #[Test]
    #[DataProvider('customPngProvider')]
    public function isGetCustomSvgWriter(string $shape, string $marker, string $cursor): void
    {
        $writer = $this->writerManager->getWriter('svg', [
            'Shape' => $shape,
            'Marker' => $marker,
            'Cursor' => $cursor,
        ]);

        $this->assertInstanceOf(CustomSvgWriter::class, $writer);
    }

    /**
     * Test that a single custom key still triggers a custom writer
     */
    #[Test]
    public function isSingleCustomKeyTriggersCustomWriter(): void
    {
        $pngWriter = $this->writerManager->getWriter('png', ['Shape' => 'S3']);
        $svgWriter = $this->writerManager->getWriter('svg', ['Marker' => 'M2']);

        $this->assertInstanceOf(CustomPngWriter::class, $pngWriter);
        $this->assertInstanceOf(CustomSvgWriter::class, $svgWriter);
    }

    /**
     * Provides invalid custom patterns
     */
    public static function invalidPatternProvider(): array
    {
        return [
            'invalid shape prefix' => [['Shape' => 'X1']],
            'invalid marker digits' => [['Marker' => 'M']],
            'invalid cursor letters' => [['Cursor' => 'Cursor1']],
            'shape without number' => [['Shape' => 'S']],
            'marker with letters' => [['Marker' => 'MAB']],
        ];
    }

    /**
     * Test invalid custom patterns throw InvalidArgumentException
     */
    #[Test]
    #[DataProvider('invalidPatternProvider')]
    public function isInvalidCustomPatternThrowsException(array $customs): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->writerManager->getWriter('png', $customs);
    }

    /**
     * Test custom writers are not supported for gif
     */
    #[Test]
    public function isCustomWriterNotSupportedForGif(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom writers not supported for 'gif'");

        $this->writerManager->getWriter('gif', ['Shape' => 'S1']);
    }

    /**
     * Test empty customs array returns a standard writer
     */
    #[Test]
    public function isEmptyCustomsReturnsStandardWriter(): void
    {
        $writer = $this->writerManager->getWriter('png', []);
        $this->assertInstanceOf(PngWriter::class, $writer);
    }

    /**
     * Test unrelated custom keys do not trigger custom writer
     */
    #[Test]
    public function isUnrelatedCustomKeysReturnStandardWriter(): void
    {
        $writer = $this->writerManager->getWriter('png', ['Foo' => 'Bar']);
        $this->assertInstanceOf(PngWriter::class, $writer);
    }
}
