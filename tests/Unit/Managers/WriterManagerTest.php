<?php

namespace HeroQR\Tests\Unit\Managers;

use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};
use Endroid\QrCode\Writer\{PngWriter, WebPWriter, SvgWriter, PdfWriter, GifWriter, EpsWriter, BinaryWriter};
use HeroQR\{Contracts\Managers\AbstractWriterManager, Core\Writers\CustomPngWriter, Managers\WriterManager};

/**
 * Class WriterManagerTest
 * Tests the WriterManager class
 */
class WriterManagerTest extends TestCase
{
    private WriterManager $writerManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->writerManager = new WriterManager();

        $this->assertInstanceOf(AbstractWriterManager::class, $this->writerManager);
    }

    public static function writerFormatProvider() : array
    {
        return [
            'PNG Writer' => ['png', PngWriter::class],
            'SVG Writer' => ['svg', SvgWriter::class],
            'PDF Writer' => ['pdf', PdfWriter::class],
            'GIF Writer' => ['gif', GifWriter::class],
            'EPS Writer' => ['eps', EpsWriter::class],
            'WEBP Writer' => ['webp', WebPWriter::class],
            'BINARY Writer' => ['binary', BinaryWriter::class],
        ];
    }
    
    /** 
     * Test that the getWriter method returns a standard Writer
     */
    #[Test]
    #[DataProvider('writerFormatProvider')]
    public function isGetWriterStandard(string $format, string $expectedClass): void
    {
        $writer = $this->writerManager->getWriter($format);

        $this->assertInstanceOf($expectedClass, $writer, "Writer for format '{$format}' is not an instance of expected class.");
    }

    /** 
     * Test that the getWriter method returns a custom Writer
     */
    #[Test]
    public function isGetWriterWithCustomParameters()
    {
        $getWriter = $this->writerManager->getWriter('png', [
            'Marker' => 'M1',
            'Cursor' => 'C1',
            'Shape' => 'S1'
        ]);

        $this->assertIsObject($getWriter);
        $this->assertInstanceOf(CustomPngWriter::class, $getWriter);
    }

    /**
     * Test that the getWriter method throws an exception when unsupported custom format
     */
    #[Test]
    public function isGetWriterWithInvalidCustomParameters()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom writers not supported for 'svg'");

        $customs = ['Marker' => 'M1'];
        $this->writerManager->getWriter('svg', $customs);
    }

    /**
     * Test that the getWriter method throws an exception when an unsupported standard format
     */
    #[Test]
    public function isGetWriterWithInvalidFormat()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unsupported format 'txt'. Supported formats: png, svg, eps, pdf, binary, webp, gif");

        $this->writerManager->getWriter('txt');
    }
}