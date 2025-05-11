<?php

namespace HeroQR\Tests\Unit\Managers;

use PHPUnit\Framework\{Attributes\Test, TestCase};
use HeroQR\{Contracts\Managers\ColorManagerInterface, Managers\ColorManager};

/**
 * Class ColorManagerTest
 * Tests the ColorManager class.
 */
class ColorManagerTest extends TestCase
{
    private ColorManager $colorManager;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->colorManager = new ColorManager();

        $this->assertInstanceOf(ColorManagerInterface::class, $this->colorManager, 'The colorManager must implement ColorInterface');
    }

    /**
     * Test that the default colors are set correctly in the ColorManager
     */
    #[Test]
    public function isGetdefaultColors(): void
    {
        $colorManager = $this->colorManager;

        $defaultColor = $colorManager->getColor();
        $this->assertEquals([0, 0, 0], [$defaultColor->getRed(), $defaultColor->getGreen(), $defaultColor->getBlue()], 'The default QR code color should be black');

        $defaultBackgroundColor = $colorManager->getBackgroundColor();
        $this->assertEquals([255, 255, 255], [$defaultBackgroundColor->getRed(), $defaultBackgroundColor->getGreen(), $defaultBackgroundColor->getBlue()], 'The default background color should be white.');

        $defaultLabelColor = $colorManager->getLabelColor();
        $this->assertEquals([0, 0, 0], [$defaultLabelColor->getRed(), $defaultLabelColor->getGreen(), $defaultLabelColor->getBlue()], 'The default label color should be black.');
    }

    /**
     * Test setting and retrieving a custom QR code color
     */
    #[Test]
    public function isSetAndGetColor(): void
    {
        $colorManager = $this->colorManager;

        $colorManager->setColor('#FF5733');
        $customColor = $colorManager->getColor();

        $this->assertEquals([255, 87, 51], [$customColor->getRed(), $customColor->getGreen(), $customColor->getBlue()], 'The custom QR code color should match the provided hex value');
    }

    /**
     * Test setting and retrieving a custom background color
     */
    #[Test]
    public function isSetAndGetBackgroundColor(): void
    {
        $colorManager = $this->colorManager;

        $colorManager->setBackgroundColor('#33FF57');
        $backgroundColor = $colorManager->getBackgroundColor();

        $this->assertEquals([51, 255, 87], [$backgroundColor->getRed(), $backgroundColor->getGreen(), $backgroundColor->getBlue()], 'The custom background color should match the provided hex value');
    }

    /**
     * Test setting and retrieving a custom label color
     */
    #[Test]
    public function isSetAndGetLabelColor(): void
    {
        $colorManager = $this->colorManager;

        $colorManager->setLabelColor('#3357FF');
        $labelColor = $colorManager->getLabelColor();

        $this->assertEquals([51, 87, 255], [$labelColor->getRed(), $labelColor->getGreen(), $labelColor->getBlue()], 'The custom label color should match the provided hex value');
    }

    /**
     * Test the behavior of the hex2rgb method with an invalid hex color
     */
    #[Test]
    public function isHex2RgbInvalidColor(): void
    {
        $colorManager = $this->colorManager;

        $reflection = new \ReflectionClass(ColorManager::class);
        $hex2rgbMethod = $reflection->getMethod('hex2rgb');

        $fallbackColor = $hex2rgbMethod->invokeArgs($colorManager, ['fffffff']);

        $this->assertNotNull($fallbackColor, 'Expected a fallback color object');
        $this->assertEquals([0, 0, 0], [$fallbackColor->getRed(), $fallbackColor->getGreen(), $fallbackColor->getBlue()], 'Invalid hex input should fallback to black color [0, 0, 0]');
    }

    /**
     * Test the behavior of the hex2rgb method with a valid hex color
     */
    #[Test]
    public function isHex2RgbValidColor(): void
    {
        $colorManager = $this->colorManager;

        $reflection = new \ReflectionClass(ColorManager::class);
        $hex2rgbMethod = $reflection->getMethod('hex2rgb');

        $color = $hex2rgbMethod->invokeArgs($colorManager, ['#EFEFEF']);

        $this->assertNotNull($color, 'Expected a valid color object');
        $this->assertEquals([239, 239, 239], [$color->getRed(), $color->getGreen(), $color->getBlue()], 'The hex color #EFEFEF should correctly convert to RGB [239, 239, 239]');
    }

    /**
     * Test the behavior of the hex2rgb method with a valid hex color with alpha channel
     */
    #[Test]
    public function isHex2RgbaValidColor(): void
    {
        $colorManager = $this->colorManager;

        $reflection = new \ReflectionClass(ColorManager::class);
        $hex2rgbMethod = $reflection->getMethod('hex2rgb');

        $color = $hex2rgbMethod->invokeArgs($colorManager, ['#FF000080']);

        $this->assertNotNull($color, 'Expected a valid color object');
        $this->assertEquals([255, 0, 0, 128], [$color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()], 'RGBA values should correctly reflect the hex input #FF000080');
    }

    /**
     * Test the behavior of the hex2rgb method with an invalid hex color with alpha channel
     */
    #[Test]
    public function isHex2RgbaInvalidColor(): void
    {
        $colorManager = $this->colorManager;

        $reflection = new \ReflectionClass(ColorManager::class);
        $hex2rgbMethod = $reflection->getMethod('hex2rgb');

        $color = $hex2rgbMethod->invokeArgs($colorManager, ['#FF5733GG']);

        $this->assertNotNull($color, 'Expected a valid color object');
        $this->assertEquals([0, 0, 0, 0], [$color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()], 'Invalid hex color should return default black color [0, 0, 0, 0]');
    }
}
