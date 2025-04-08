<?php

namespace HeroQR\Customs;

/**
 * A class that overlays a background image with a custom cursor image, providing functionality
 * to save, display, or return the result as a base64 string.
 *
 * @package HeroQR\Customs
 */
class ImageOverlay
{
    private ?string $backgroundPath;
    private ?string $overlayPath;

    /**
     * Constructor to initialize background and overlay paths
     */
    public function __construct($background, $overlay)
    {
        $this->overlayPath = CursorPaths::getValueByKey($overlay);
        $this->backgroundPath = MarkerPaths::getValueByKey($background);
    }

    /**
     * Validates the paths for background and overlay
     */
    private function validatePaths(): void
    {
        if (!file_exists($this->backgroundPath) || !file_exists($this->overlayPath)) {
            throw new \Exception('Invalid file paths for background or overlay images.');
        }
    }

    /**
     * Creates an image resource from a file path
     */
    private function createImageResource(string $path): \GdImage
    {
        $image = imagecreatefrompng($path);

        if (!$image) {
            throw new \Exception('Failed to create image resource.');
        }

        return $image;
    }

    /**
     * Creates a centered image by overlaying one image on another
     */
    private function createCenteredImage(): \GdImage
    {
        $this->validatePaths();

        $background = $this->createImageResource($this->backgroundPath);
        $overlay = $this->createImageResource($this->overlayPath);

        $bgWidth = imagesx($background);
        $bgHeight = imagesy($background);
        $overlayWidth = imagesx($overlay) / 3.1;
        $overlayHeight = imagesy($overlay) / 3.1;

        $x = ($bgWidth - $overlayWidth) / 2;
        $y = ($bgHeight - $overlayHeight) / 2;

        imagealphablending($background, true);
        imagesavealpha($background, true);

        imagecopyresampled(
            $background,
            $overlay,
            (int)$x,
            (int)$y,
            0,
            0,
            (int)$overlayWidth,
            (int)$overlayHeight,
            imagesx($overlay),
            imagesy($overlay)
        );

        return $background;
    }

    /**
     * Saves the generated image to the specified output path
     */
    public function saveImage(string $outputPath): void
    {
        $result = $this->createCenteredImage();

        imagepng($result, $outputPath);
        imagedestroy($result);
    }

    /**
     * Returns the image as a base64-encoded URI
     */
    public function getUriImage(): string
    {
        return $this->getImageAsBase64();
    }

    /**
     * Returns the image as a string
     */
    public function getImageAsString(): string
    {
        return $this->getImageAsBase64(false);
    }

    /**
     * Helper method to generate base64 image output
     */
    private function getImageAsBase64(bool $uri = true): string
    {
        $image = $this->createCenteredImage();

        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);

        return $uri ? 'data:image/png;base64,' . base64_encode($data) : $data;
    }

    /**
     * Returns the generated image as a GdImage instance
     */
    public function getImage(): \GdImage
    {
        return $this->createCenteredImage();
    }

    /**
     * Outputs the image directly to the browser
     */
    public function outputImage(): void
    {
        $result = $this->createCenteredImage();

        header('Content-Type: image/png');
        imagepng($result);
        imagedestroy($result);
    }
}
