<?php

namespace HeroQR\Managers;

use HeroQR\Contracts\Managers\OutputManagerInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Matrix\Matrix;
use InvalidArgumentException;

class OutputManager implements OutputManagerInterface
{
    /**
     * Save the QR Code output to a file.
     *
     * @param ResultInterface $builder
     * @param string $path
     * @return bool
     * @throws InvalidArgumentException if the format is unsupported or saving fails.
     */
    public function saveTo(ResultInterface $builder, string $path): bool
    {
        $format = strtolower($builder->getMimeType());

        $extension = match ($format) {
            'image/png' => '.png',
            'image/gif' => '.gif',
            'image/svg+xml' => '.svg',
            'image/webp' => '.webp',
            'image/eps' => '.eps',
            'application/pdf' => '.pdf',
            'application/postscript' => '.eps',
            'application/octet-stream' => '.bin',
            'text/plain' => '.bin',
            default => throw new InvalidArgumentException('Unsupported format.'),
        };

        $fullPath = $path . $extension;

        if (!$builder->saveToFile($fullPath) && !file_exists($fullPath)) {
            throw new InvalidArgumentException('Saving to file failed');
        }

        return true;
    }

    /**
     * Return the data URI for the QR Code.
     *
     * @param ResultInterface $builder
     * @return string
     */
    public function getDataUri(ResultInterface $builder): string
    {
        return $builder->getDataUri();
    }

    /**
     * Convert the QR Code matrix to a two-dimensional array.
     *
     * @param ResultInterface $builder
     * @return array
     */
    public function getMatrixAsArray(ResultInterface $builder): array
    {
        $matrix = $builder->getMatrix();
        $matrixArray = [];

        for ($row = 0; $row < $matrix->getBlockCount(); $row++) {
            for ($col = 0; $col < $matrix->getBlockCount(); $col++) {
                $matrixArray[$row][$col] = $matrix->getBlockValue($row, $col);
            }
        }

        return $matrixArray;
    }

    /**
     * Return the QR Code matrix as a Matrix object.
     *
     * @param ResultInterface $builder
     * @return Matrix
     */
    public function getMatrix(ResultInterface $builder): Matrix
    {
        return $builder->getMatrix();
    }

    /**
     * Return the QR Code output as a string.
     *
     * @param ResultInterface $builder
     * @return string
     */
    public function getString(ResultInterface $builder): string
    {
        return $builder->getString();
    }
}
