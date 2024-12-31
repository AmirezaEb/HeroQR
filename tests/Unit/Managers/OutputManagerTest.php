<?php

namespace HeroQR\Tests\Unit\Managers;

use Endroid\QrCode\QrCode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Endroid\QrCode\Matrix\Matrix;
use HeroQR\Managers\OutputManager;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\PngWriter;
use PHPUnit\Framework\Attributes\Test;
use Endroid\QrCode\Writer\WriterInterface;

/**
 * Class OutputManagerTest
 * Tests the OutputManager class.
 */
class OutputManagerTest extends TestCase
{
    private QrCodeInterface $qrCodeGenerator;
    private OutputManager $outputManager;

    /** 
     * Setup method
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->qrCodeGenerator = new QrCode('https://example.com');
        $this->outputManager = new OutputManager();
    }

    /**
     * Test saving QR code to different formats
     */
    #[Test]
    public function isSaveTo(): void
    {
        $dataTypes = ['png', 'gif', 'binary', 'eps', 'svg', 'webp'];

        foreach ($dataTypes as $dataType) {
            $writer = $this->validateWriter($dataType);

            $fileExtension = ($dataType === 'binary') ? 'bin' : $dataType;
            $result = $writer->write($this->qrCodeGenerator);
            $filePath = './Qr-' . $fileExtension;

            $isSaved = $this->outputManager->saveTo($result, $filePath);

            $this->assertTrue($isSaved, "Failed To Save QR Code In Format: $fileExtension");
            $this->assertFileExists($filePath . '.' . $fileExtension);

            $this->deleteFile($filePath . '.' . $fileExtension);
        }
    }

    /**
     * Test saving QR code to an unsupported format
     */
    #[Test]
    public function isSaveToUnsupportedFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Format "mp4" Does Not Exist');

        $this->validateWriter('mp4');
    }

    /**
     * Test generating a data URI for QR code
     */
    #[Test]
    public function isGetDataUri(): void
    {
        $writer = new PngWriter();
        $dataUri = $writer->write($this->qrCodeGenerator)->getDataUri();

        $this->assertStringStartsWith('data:image/png;base64', $dataUri, 'Data URI Should Start With The Correct Prefix');
    }

    /**
     * Test converting QR code matrix to an array
     */
    #[Test]
    public function isGetMatrixAsArray(): void
    {
        $matrix = (new PngWriter())->write($this->qrCodeGenerator)->getMatrix();

        $matrixArray = [];

        for ($row = 0; $row < 6; $row++) {
            for ($col = 0; $col < $matrix->getBlockCount(); $col++) {
                $matrixArray[$row][$col] = $matrix->getBlockValue($row, $col);
            }
        }

        $expectedMatrix = [
            0 => [1, 1, 1, 1, 1, 1, 1, 0, 1, 0, 1, 0, 1, 0, 0, 0, 1, 0, 1, 1, 1, 1, 1, 1, 1],
            1 => [1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1],
            2 => [1, 0, 1, 1, 1, 0, 1, 0, 1, 1, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0, 1, 1, 1, 0, 1],
            3 => [1, 0, 1, 1, 1, 0, 1, 0, 1, 1, 1, 0, 1, 1, 0, 1, 1, 0, 1, 0, 1, 1, 1, 0, 1],
            4 => [1, 0, 1, 1, 1, 0, 1, 0, 1, 0, 0, 1, 1, 0, 0, 0, 1, 0, 1, 0, 1, 1, 1, 0, 1],
            5 => [1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1],
        ];

        $this->assertInstanceOf(Matrix::class, $matrix);
        $this->assertIsArray($matrixArray);
        $this->assertEquals($expectedMatrix, $matrixArray);
    }

    /**
     * Test retrieving the QR code matrix object
     */
    #[Test]
    public function isGetMatrix(): void
    {
        $matrix = (new PngWriter())->write($this->qrCodeGenerator)->getMatrix();

        $this->assertInstanceOf(Matrix::class, $matrix, 'The Returned Object Should Be An Instance Of Matrix');
        $this->assertGreaterThan(0, $matrix->getBlockCount(), 'The matrix should contain blocks.');

        for ($row = 0; $row < $matrix->getBlockCount(); $row++) {
            for ($col = 0; $col < $matrix->getBlockCount(); $col++) {
                $this->assertContains($matrix->getBlockValue($row, $col), [0, 1], "Unexpected Value In Matrix At ($row, $col)");
            }
        }
    }

    /**
     * Test converting QR code to string
     */
    #[Test]
    public function isGetString(): void
    {
        $qrCodeString = (new PngWriter())->write($this->qrCodeGenerator)->getString();

        $this->assertIsString($qrCodeString, 'QR Code Output Should Be A String');
        $this->assertStringStartsWith("\x89PNG", $qrCodeString, 'QR Code String Should Start With The PNG Header');
    }

    /**
     * Validate and retrieve the writer instance for a given format
     */
    private function validateWriter(string $format): WriterInterface
    {
        $writerClass = 'Endroid\\QrCode\\Writer\\' . ucfirst($format) . 'Writer';

        if (!class_exists($writerClass)) {
            throw new InvalidArgumentException(sprintf('Format "%s" Does Not Exist', $format));
        }

        $writer = new $writerClass();

        if (!$writer instanceof WriterInterface) {
            throw new InvalidArgumentException(sprintf('Format "%s" Is Not Supported', $format));
        }

        return $writer;
    }

    /**
     * Delete a file if it exists
     */
    private function deleteFile(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
