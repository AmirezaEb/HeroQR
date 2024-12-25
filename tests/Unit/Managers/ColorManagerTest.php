<?php

namespace HeroQR\Tests\Managers;

use PHPUnit\Framework\TestCase;
use HeroQR\Managers\ColorManager;
use PHPUnit\Framework\Attributes\Test;
use Endroid\QrCode\Color\ColorInterface;
use HeroQR\Contracts\Managers\ColorManagerInterface;

/**
 * Class ColorManagerTest
 * Tests the functionality and behavior of the ColorManager class,
 * including default values, custom settings, and error handling.
 */
class ColorManagerTest extends TestCase
{
    private ColorManagerInterface $colorManager;

    /**
     * Set up the test environment by initializing a ColorManager instance.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->colorManager = new ColorManager();
    }

    /**
     * Test that the default colors are set correctly in the ColorManager.
     * Verifies the default QR code color, background color, and label color.
     */
    #[Test]
    public function defaultColors(): void
    {
        $colorManager = $this->colorManager;

        $this->assertInstanceOf(ColorManagerInterface::class, $colorManager);

        $defaultColor = $colorManager->getColor();
        $this->assertInstanceOf(ColorInterface::class, $defaultColor);
        $this->assertEquals([0, 0, 0], [$defaultColor->getRed(), $defaultColor->getGreen(), $defaultColor->getBlue()]);

        $defaultBackgroundColor = $colorManager->getBackgroundColor();
        $this->assertInstanceOf(ColorInterface::class, $defaultBackgroundColor);
        $this->assertEquals([255, 255, 255], [$defaultBackgroundColor->getRed(), $defaultBackgroundColor->getGreen(), $defaultBackgroundColor->getBlue()]);

        $defaultLabelColor = $colorManager->getLabelColor();
        $this->assertInstanceOf(ColorInterface::class, $defaultLabelColor);
        $this->assertEquals([0, 0, 0], [$defaultLabelColor->getRed(), $defaultLabelColor->getGreen(), $defaultLabelColor->getBlue()]);
    }

    /**
     * Test setting and retrieving a custom QR code color.
     * Ensures that the correct color values are applied and retrieved.
     */
    #[Test]
    public function setAndGetColor(): void
    {
        $colorManager = $this->colorManager;

        $colorManager->setColor('#FF5733');

        $customColor = $colorManager->getColor();
        $this->assertInstanceOf(ColorInterface::class, $customColor);
        $this->assertEquals([255, 87, 51], [$customColor->getRed(), $customColor->getGreen(), $customColor->getBlue()]);
    }

    /**
     * Test setting and retrieving a custom background color.
     * Ensures the background color is updated correctly.
     */
    #[Test]
    public function setAndGetBackgroundColor(): void
    {
        $colorManager = $this->colorManager;

        $colorManager->setBackgroundColor('#33FF57');

        $backgroundColor = $colorManager->getBackgroundColor();
        $this->assertInstanceOf(ColorInterface::class, $backgroundColor);
        $this->assertEquals([51, 255, 87], [$backgroundColor->getRed(), $backgroundColor->getGreen(), $backgroundColor->getBlue()]);
    }

    /**
     * Test setting and retrieving a custom label color.
     * Ensures the label color is updated correctly.
     */
    #[Test]
    public function setAndGetLabelColor(): void
    {
        $colorManager = $this->colorManager;

        $colorManager->setLabelColor('#3357FF');

        $labelColor = $colorManager->getLabelColor();
        $this->assertInstanceOf(ColorInterface::class, $labelColor);
        $this->assertEquals([51, 87, 255], [$labelColor->getRed(), $labelColor->getGreen(), $labelColor->getBlue()]);
    }

    /**
     * Test the behavior of the hex2rgb method with an invalid hex color.
     * Verifies that invalid input returns a default color (black).
     */
    #[Test]
    public function hex2RgbInvalidColor(): void
    {
        $colorManager = $this->colorManager;

        $reflection = new \ReflectionClass(ColorManager::class);
        $hex2rgbMethod = $reflection->getMethod('hex2rgb');
        $hex2rgbMethod->setAccessible(true);

        $invalidColor = $hex2rgbMethod->invokeArgs($colorManager, ['invalid']);
        $this->assertEquals([0, 0, 0], [$invalidColor->getRed(), $invalidColor->getGreen(), $invalidColor->getBlue()]);
    }
}
