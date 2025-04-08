<?php

namespace HeroQR\Contracts\Managers;

use Endroid\QrCode\{Matrix\Matrix,Writer\Result\ResultInterface};

/**
 * Interface OutputManagerInterface
 *
 * This interface defines methods for managing QR Code output operations, including
 * saving the QR code to a file, retrieving its data URI, and getting the matrix
 * representation of the QR code.
 *
 * @package HeroQR\Contracts\Managers
 */
interface OutputManagerInterface
{
    /**
     * Save the QR Code output to a file
     *
     * @param ResultInterface $builder
     * @param string $path
     * @return bool
     * @throws \InvalidArgumentException if the format is unsupported or saving fails
     */
    public function saveTo(ResultInterface $builder, string $path): bool;

    /**
     * Return the data URI for the QR Code
     *
     * @param ResultInterface $builder
     * @return string
     */
    public function getDataUri(ResultInterface $builder): string;

    /**
     * Convert the QR Code matrix to a two-dimensional array
     *
     * @param ResultInterface $builder
     * @return array
     */
    public function getMatrixAsArray(ResultInterface $builder): array;

    /**
     * Return the QR Code matrix as a Matrix object
     *
     * @param ResultInterface $builder
     * @return Matrix
     */
    public function getMatrix(ResultInterface $builder): Matrix;

    /**
     * Return the QR Code output as a string
     *
     * @param ResultInterface $builder
     * @return string
     */
    public function getString(ResultInterface $builder): string;
}
