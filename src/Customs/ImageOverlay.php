<?php

namespace HeroQR\Customs;

use Endroid\QrCode\Color\ColorInterface;

/**
 * Handles overlaying a cursor image on a background image
 * Provides methods to save, output, or get the image as a Base64 string
 */
class ImageOverlay
{
    private string $backgroundPath;
    private string $overlayPath;

    /**
     * Constructor to initialize background and overlay paths
     *
     * @param $background
     * @param $overlay
     */
    public function __construct(string $background, string $overlay)
    {
        $this->overlayPath = CursorPaths::getValueByKey($overlay);
        $this->backgroundPath = MarkerPaths::getValueByKey($background);
    }

    /**
     * Saves the generated image to the specified output path
     *
     * @param string $outputPath
     * @param ColorInterface|null $overlayColor
     * @return void
     * @throws \Exception
     */
    public function saveImage(string $outputPath, ?ColorInterface $overlayColor = null): void
    {
        $result = $this->createCenteredImage($overlayColor);

        imagepng($result, $outputPath);
        imagedestroy($result);
    }

    /**
     * Returns the image as a base64-encoded URI
     *
     * @param ColorInterface|null $overlayColor
     * @return string
     * @throws \Exception
     */
    public function getUriImage(?ColorInterface $overlayColor = null): string
    {
        return $this->getImageAsBase64(true, $overlayColor);
    }

    /**
     * Returns the image as a string
     *
     * @param ColorInterface|null $overlayColor
     * @return string
     * @throws \Exception
     */
    public function getImageAsString(?ColorInterface $overlayColor = null): string
    {
        return $this->getImageAsBase64(false, $overlayColor);
    }

    /**
     * Returns the generated image as a GdImage instance
     *
     * @param ColorInterface|null $overlayColor
     * @return \GdImage
     * @throws \Exception
     */
    public function getImage(?ColorInterface $overlayColor = null): \GdImage
    {
        return $this->createCenteredImage($overlayColor);
    }

    /**
     * Outputs the image directly to the browser
     *
     * @param ColorInterface|null $overlayColor
     * @return void
     * @throws \Exception
     */
    public function outputImage(?ColorInterface $overlayColor = null): void
    {
        $result = $this->createCenteredImage($overlayColor);

        header('Content-Type: image/png');
        imagepng($result);
        imagedestroy($result);
    }

    /**
     * Creates a centered image by overlaying one image on another
     *
     * @param \Endroid\QrCode\Color\ColorInterface|null $color
     * @return \GdImage
     * @throws \Exception
     */
    private function createCenteredImage(?ColorInterface $color = null): \GdImage
    {
        $background = $this->createImageResource($this->backgroundPath);
        $overlay = $this->createImageResource($this->overlayPath);

        if ($color !== null) {
            $background = $this->recolorImage($background, $color);
            $overlay = $this->recolorImage($overlay, $color);
        }

        $bgWidth = imagesx($background);
        $bgHeight = imagesy($background);

        $overlayOriginalWidth = imagesx($overlay);
        $overlayOriginalHeight = imagesy($overlay);

        $overlayWidth = (int)($overlayOriginalWidth / 3.1);
        $overlayHeight = (int)($overlayOriginalHeight / 3.1);

        $x = (int)(($bgWidth - $overlayWidth) / 2);
        $y = (int)(($bgHeight - $overlayHeight) / 2);

        imagealphablending($background, true);
        imagesavealpha($background, true);

        imagecopyresampled(
            $background, $overlay,
            $x, $y, 0, 0,
            $overlayWidth, $overlayHeight,
            $overlayOriginalWidth, $overlayOriginalHeight
        );

        imagedestroy($overlay);

        return $background;
    }

    /**
     * Helper method to generate base64 image output
     *
     * @param bool $uri
     * @param \Endroid\QrCode\Color\ColorInterface|null $overlayColor
     * @return string
     * @throws \Exception
     */
    private function getImageAsBase64(bool $uri = true, ?ColorInterface $overlayColor = null): string
    {
        $image = $this->createCenteredImage($overlayColor);

        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);

        return $uri ? 'data:image/png;base64,' . base64_encode($data) : $data;
    }

    /**
     * Tints an image with a specific color while preserving transparency
     *
     * @param \GdImage $image
     * @param \Endroid\QrCode\Color\ColorInterface $color
     * @return \GdImage
     */
    private function recolorImage(\GdImage $image, ColorInterface $color): \GdImage
    {
        [$width, $height] = [imagesx($image), imagesy($image)];

        $tinted = imagecreatetruecolor($width, $height);

        imagealphablending($tinted, false);
        imagesavealpha($tinted, true);

        $transparentColor = imagecolorallocatealpha($tinted, 0, 0, 0, 127);

        imagefill($tinted, 0, 0, $transparentColor);

        $newColor = imagecolorallocatealpha(
            $tinted,
            $color->getRed(),
            $color->getGreen(),
            $color->getBlue(),
            $color->getAlpha()
        );

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                $pixel = imagecolorat($image, $x, $y);

                if ((($pixel >> 24) & 0x7F) < 127) {
                    imagesetpixel($tinted, $x, $y, $newColor);
                }
            }
        }

        imagedestroy($image);

        return $tinted;
    }

    /**
     * Creates an image resource from a file path
     *
     * @param string $path
     * @return \GdImage
     * @throws \Exception
     */
    private function createImageResource(string $path): \GdImage
    {
        if (!file_exists($path)) {
            throw new \Exception("File not found: {$path}");
        }

        $image = imagecreatefrompng($path);

        if (!$image) {
            throw new \Exception("Failed to load image from: {$path}");
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        return $image;
    }
}
