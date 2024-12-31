<?php

namespace HeroQR\Tests\Unit\Managers;

use PHPUnit\Framework\TestCase;
use HeroQR\Managers\ColorManager;
use PHPUnit\Framework\Attributes\Test;
use Endroid\QrCode\Color\ColorInterface;
use HeroQR\Contracts\Managers\ColorManagerInterface;

/**
 * Class ColorManagerTest
 * Tests the ColorManager class.
 */
class ColorManagerTest extends TestCase
{
    private ColorManagerInterface $colorManager;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->colorManager = new ColorManager();
    }

    /**
     * Test that the default colors are set correctly in the ColorManager
     */
    #[Test]
    public function isGetdefaultColors(): void
    {
        $colorManager = $this->colorManager;

        $this->assertInstanceOf(ColorManagerInterface::class, $colorManager, 'The instance should implement ColorManagerInterface');

        $defaultColor = $colorManager->getColor();
        $this->assertInstanceOf(ColorInterface::class, $defaultColor, 'The default QR code color should be an instance of ColorInterface');
        $this->assertEquals([0, 0, 0], [$defaultColor->getRed(), $defaultColor->getGreen(), $defaultColor->getBlue()], 'The default QR code color should be black');

        $defaultBackgroundColor = $colorManager->getBackgroundColor();
        $this->assertInstanceOf(ColorInterface::class, $defaultBackgroundColor, 'The default background color should be an instance of ColorInterface');
        $this->assertEquals([255, 255, 255], [$defaultBackgroundColor->getRed(), $defaultBackgroundColor->getGreen(), $defaultBackgroundColor->getBlue()], 'The default background color should be white.');

        $defaultLabelColor = $colorManager->getLabelColor();
        $this->assertInstanceOf(ColorInterface::class, $defaultLabelColor, 'The default label color should be an instance of ColorInterface');
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
        $this->assertInstanceOf(ColorInterface::class, $customColor, 'The custom QR code color should be an instance of ColorInterface.');
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
        $this->assertInstanceOf(ColorInterface::class, $backgroundColor, 'The custom background color should be an instance of ColorInterface');
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
        $this->assertInstanceOf(ColorInterface::class, $labelColor, 'The custom label color should be an instance of ColorInterface');
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
        $hex2rgbMethod->setAccessible(true);

        $invalidColor = $hex2rgbMethod->invokeArgs($colorManager, ['invalid']);
        $this->assertEquals([0, 0, 0], [$invalidColor->getRed(), $invalidColor->getGreen(), $invalidColor->getBlue()], 'Invalid hex color should return default black color [0, 0, 0]');
    }
}
