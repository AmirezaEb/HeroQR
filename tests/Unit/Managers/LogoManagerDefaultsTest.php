<?php

namespace HeroQR\Tests\Unit\Managers;

use HeroQR\{Contracts\Managers\LogoManagerInterface, Managers\LogoManager};
use PHPUnit\Framework\{Attributes\Test, TestCase};

/**
 * Class LogoManagerDefaultsTest
 * Tests default LogoManager values and additional edge cases.
 */
class LogoManagerDefaultsTest extends TestCase
{
    private LogoManager $logoManager;

    /**
     * Initializes the LogoManager instance
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->logoManager = new LogoManager();
        $this->assertInstanceOf(LogoManagerInterface::class, $this->logoManager);
    }

    /**
     * Test default logo path is empty
     */
    #[Test]
    public function isDefaultLogoPathEmpty(): void
    {
        $this->assertSame('', $this->logoManager->getLogoPath());
    }

    /**
     * Test default logo size is 80
     */
    #[Test]
    public function isDefaultLogoSizeEighty(): void
    {
        $this->assertSame(80, $this->logoManager->getLogoSize());
    }

    /**
     * Test default logo background is disabled
     */
    #[Test]
    public function isDefaultLogoBackgroundFalse(): void
    {
        $this->assertFalse($this->logoManager->getLogoBackground());
    }

    /**
     * Test logo size zero throws an exception
     */
    #[Test]
    public function isSetLogoSizeZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Logo Size Must Be A Positive Integer');

        $this->logoManager->setLogoSize(0);
    }

    /**
     * Test setting a large valid logo size
     */
    #[Test]
    public function isSetLargeLogoSizeValid(): void
    {
        $this->logoManager->setLogoSize(500);
        $this->assertSame(500, $this->logoManager->getLogoSize());
    }

    /**
     * Test setting logo from an unreadable path throws an exception
     */
    #[Test]
    public function isSetUnreadableLogoThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->logoManager->setLogo(__DIR__ . '/does-not-exist-logo.png');
    }

    /**
     * Test replacing logo path with another valid file
     */
    #[Test]
    public function isReplaceLogoPath(): void
    {
        $first = tempnam(sys_get_temp_dir(), 'logo1');
        $second = tempnam(sys_get_temp_dir(), 'logo2');
        file_put_contents($first, 'logo-one');
        file_put_contents($second, 'logo-two');

        $this->logoManager->setLogo($first);
        $this->assertSame($first, $this->logoManager->getLogoPath());

        $this->logoManager->setLogo($second);
        $this->assertSame($second, $this->logoManager->getLogoPath());

        @unlink($first);
        @unlink($second);
    }
}
