<?php

namespace HeroQR\Tests\Unit\Managers;

use PHPUnit\Framework\TestCase;
use HeroQR\Managers\WriterManager;
use Endroid\QrCode\Writer\PngWriter;
use PHPUnit\Framework\Attributes\Test;
use HeroQR\Core\Writers\CustomPngWriter;

/**
 * Class WriterManagerTest
 * Tests the WriterManager class.
 */
class WriterManagerTest extends TestCase
{

    /** 
     * Test that the getWriter method returns a standard Writer
     */
    #[Test]
    public function isGetWriterStandard()
    {
        $writerManager = new WriterManager();

        $writer = $writerManager->getWriter('png');

        $this->assertIsObject($writer);
        $this->assertInstanceOf(PngWriter::class, $writer);
    }

    /** 
     * Test that the getWriter method returns a custom Writer
     */
    #[Test]
    public function isGetWriterWithCustomParameters()
    {
        $writerManager = new WriterManager();

        $writer = $writerManager->getWriter('png', [
            'Marker' => 'M1',
            'Cursor' => 'C1',
            'Shape' => 'S1'
        ]);

        $this->assertIsObject($writer);
        $this->assertInstanceOf(CustomPngWriter::class, $writer);
    }

    /**
     * Test that the getWriter method throws an exception when unsupported custom format
     */
    #[Test]
    public function isGetWriterWithInvalidCustomParameters()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom writers not supported for 'svg'");

        $writerManager = new WriterManager();
        $customs = ['Marker' => 'M1'];
        $writerManager->getWriter('svg', $customs);
    }

    /**
     * Test that the getWriter method throws an exception when an unsupported standard format
     */
    #[Test]
    public function isGetWriterWithInvalidFormat()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unsupported format 'txt'. Supported formats: png, svg, eps, pdf, binary, webp, gif");

        $writerManager = new WriterManager();
        $writerManager->getWriter('txt');
    }

}