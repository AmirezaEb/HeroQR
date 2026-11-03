<?php

namespace HeroQR\Tests\Unit\Managers;

use Endroid\QrCode\Color\ColorInterface;
use HeroQR\{Contracts\Managers\ColorManagerInterface, Managers\ColorManager};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class ColorManagerValidationTest
 * Extended validation and conversion tests for ColorManager.
 */
class ColorManagerValidationTest extends TestCase
{
    private ColorManager $colorManager;

    /**
     * Initializes the ColorManager instance
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->colorManager = new ColorManager();
        $this->assertInstanceOf(ColorManagerInterface::class, $this->colorManager);
    }

    /**
     * Test createColorFromRgba returns a ColorInterface instance
     */
    #[Test]
    public function isCreateColorFromRgbaReturnsColorInterface(): void
    {
        $color = $this->colorManager->createColorFromRgba([
            'Red' => 12,
            'Green' => 34,
            'Blue' => 56,
            'Alpha' => 0.5,
        ]);

        $this->assertInstanceOf(ColorInterface::class, $color);
        $this->assertEquals(12, $color->getRed());
        $this->assertEquals(34, $color->getGreen());
        $this->assertEquals(56, $color->getBlue());
    }

    /**
     * Test createColorFromRgba without Alpha defaults to fully opaque
     */
    #[Test]
    public function isCreateColorFromRgbaWithoutAlpha(): void
    {
        $color = $this->colorManager->createColorFromRgba([
            'Red' => 1,
            'Green' => 2,
            'Blue' => 3,
        ]);

        $this->assertEqualsWithDelta(1.0, $color->getOpacity(), 0.01);
    }

    /**
     * Provides invalid RGBA payloads and expected exception message fragments
     */
    public static function invalidRgbaProvider(): array
    {
        return [
            'Missing Red' => [['Green' => 10, 'Blue' => 10], 'Missing color keys'],
            'Missing Green' => [['Red' => 10, 'Blue' => 10], 'Missing color keys'],
            'Missing Blue' => [['Red' => 10, 'Green' => 10], 'Missing color keys'],
            'Red too high' => [['Red' => 300, 'Green' => 10, 'Blue' => 10], 'Red must be integer (0-255)'],
            'Green negative' => [['Red' => 10, 'Green' => -1, 'Blue' => 10], 'Green must be integer (0-255)'],
            'Blue too high' => [['Red' => 10, 'Green' => 10, 'Blue' => 256], 'Blue must be integer (0-255)'],
            'Alpha too high' => [['Red' => 10, 'Green' => 10, 'Blue' => 10, 'Alpha' => 1.5], 'Alpha must be between 0 and 1'],
            'Alpha negative' => [['Red' => 10, 'Green' => 10, 'Blue' => 10, 'Alpha' => -0.1], 'Alpha must be between 0 and 1'],
        ];
    }

    /**
     * Test invalid RGBA values throw InvalidArgumentException
     */
    #[Test]
    #[DataProvider('invalidRgbaProvider')]
    public function isInvalidRgbaThrowsException(array $rgba, string $messageFragment): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($messageFragment);

        $this->colorManager->createColorFromRgba($rgba);
    }

    /**
     * Test invalid foreground color via setColor
     */
    #[Test]
    public function isSetColorWithMissingKeysThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->colorManager->setColor(['Red' => 255]);
    }

    /**
     * Test invalid label alpha via setLabelColor
     */
    #[Test]
    public function isSetLabelColorInvalidAlphaThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Alpha must be between 0 and 1.');

        $this->colorManager->setLabelColor([
            'Red' => 10,
            'Green' => 20,
            'Blue' => 30,
            'Alpha' => 2,
        ]);
    }

    /**
     * Test setting fully transparent foreground color
     */
    #[Test]
    public function isSetFullyTransparentColor(): void
    {
        $this->colorManager->setColor([
            'Red' => 0,
            'Green' => 0,
            'Blue' => 0,
            'Alpha' => 0,
        ]);

        $color = $this->colorManager->getColor();
        $this->assertEqualsWithDelta(0.0, $color->getOpacity(), 0.01);
    }

    /**
     * Test boundary RGB values 0 and 255 are accepted
     */
    #[Test]
    public function isBoundaryRgbValuesAccepted(): void
    {
        $this->colorManager->setBackgroundColor([
            'Red' => 0,
            'Green' => 255,
            'Blue' => 0,
            'Alpha' => 1,
        ]);

        $color = $this->colorManager->getBackgroundColor();
        $this->assertEquals([0, 255, 0], [$color->getRed(), $color->getGreen(), $color->getBlue()]);
    }
}
