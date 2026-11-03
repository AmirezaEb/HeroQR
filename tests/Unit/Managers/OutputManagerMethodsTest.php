<?php

namespace HeroQR\Tests\Unit\Managers;

use Endroid\QrCode\{QrCode, Writer\PngWriter, Writer\SvgWriter};
use HeroQR\{Contracts\Managers\OutputManagerInterface, Managers\OutputManager};
use PHPUnit\Framework\{Attributes\Test, TestCase};

/**
 * Class OutputManagerMethodsTest
 * Tests OutputManager methods through the manager itself rather than writers alone.
 */
class OutputManagerMethodsTest extends TestCase
{
    private OutputManager $outputManager;
    private QrCode $qrCode;

    /**
     * Configures OutputManager and a sample QR code
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->outputManager = new OutputManager();
        $this->qrCode = new QrCode('https://heroqr.test');

        $this->assertInstanceOf(OutputManagerInterface::class, $this->outputManager);
    }

    /**
     * Test getDataUri via OutputManager for PNG
     */
    #[Test]
    public function isGetDataUriViaOutputManager(): void
    {
        $result = (new PngWriter())->write($this->qrCode);
        $dataUri = $this->outputManager->getDataUri($result);

        $this->assertStringStartsWith('data:image/png;base64', $dataUri);
        $this->assertNotEmpty($dataUri);
    }

    /**
     * Test getDataUri via OutputManager for SVG
     */
    #[Test]
    public function isGetDataUriViaOutputManagerSvg(): void
    {
        $result = (new SvgWriter())->write($this->qrCode);
        $dataUri = $this->outputManager->getDataUri($result);

        $this->assertStringStartsWith('data:image/svg+xml;base64', $dataUri);
    }

    /**
     * Test getString via OutputManager returns PNG binary
     */
    #[Test]
    public function isGetStringViaOutputManager(): void
    {
        $result = (new PngWriter())->write($this->qrCode);
        $string = $this->outputManager->getString($result);

        $this->assertIsString($string);
        $this->assertStringStartsWith("\x89PNG", $string);
    }

    /**
     * Test getMatrix via OutputManager
     */
    #[Test]
    public function isGetMatrixViaOutputManager(): void
    {
        $result = (new PngWriter())->write($this->qrCode);
        $matrix = $this->outputManager->getMatrix($result);

        $this->assertGreaterThan(0, $matrix->getBlockCount());
    }

    /**
     * Test getMatrixAsArray via OutputManager returns a square matrix of 0/1 values
     */
    #[Test]
    public function isGetMatrixAsArrayViaOutputManager(): void
    {
        $result = (new PngWriter())->write($this->qrCode);
        $matrixArray = $this->outputManager->getMatrixAsArray($result);

        $this->assertIsArray($matrixArray);
        $this->assertCount(count($matrixArray), $matrixArray[0]);

        foreach ($matrixArray as $row) {
            foreach ($row as $value) {
                $this->assertContains($value, [0, 1]);
            }
        }
    }

    /**
     * Test saveTo via OutputManager for PNG
     */
    #[Test]
    public function isSaveToViaOutputManagerPng(): void
    {
        $path = './output_manager_png_' . uniqid();
        $result = (new PngWriter())->write($this->qrCode);

        $saved = $this->outputManager->saveTo($result, $path);
        $file = $path . '.png';

        $this->assertTrue($saved);
        $this->assertFileExists($file);
        $this->assertNotEmpty(file_get_contents($file));

        unlink($file);
    }

    /**
     * Test saveTo via OutputManager for SVG
     */
    #[Test]
    public function isSaveToViaOutputManagerSvg(): void
    {
        $path = './output_manager_svg_' . uniqid();
        $result = (new SvgWriter())->write($this->qrCode);

        $saved = $this->outputManager->saveTo($result, $path);
        $file = $path . '.svg';

        $this->assertTrue($saved);
        $this->assertFileExists($file);
        $this->assertStringContainsString('<svg', file_get_contents($file));

        unlink($file);
    }

    /**
     * Test matrix array size matches matrix block count
     */
    #[Test]
    public function isMatrixArraySizeMatchesBlockCount(): void
    {
        $result = (new PngWriter())->write($this->qrCode);
        $matrix = $this->outputManager->getMatrix($result);
        $matrixArray = $this->outputManager->getMatrixAsArray($result);

        $this->assertCount($matrix->getBlockCount(), $matrixArray);
        $this->assertCount($matrix->getBlockCount(), $matrixArray[0]);
    }
}
