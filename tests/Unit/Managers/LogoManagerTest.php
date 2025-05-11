<?php

namespace HeroQR\Tests\Unit\Managers;

use HeroQR\Managers\LogoManager;
use PHPUnit\Framework\{TestCase,Attributes\Test};
use HeroQR\Contracts\Managers\LogoManagerInterface;

/**
 * Class LogoManagerTest
 * Tests the LogoManager class.
 */
final class LogoManagerTest extends TestCase
{
    private LogoManager $logoManager;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->logoManager = new LogoManager();

        $this->assertInstanceOf(LogoManagerInterface::class, $this->logoManager, 'The LogoManager must implement LogoManagerInterface');
    }

    /**
     * Test setting and getting the logo path with a valid file
     */
    #[Test]
    public function isSetAndGetLogoPath(): void
    {
        $logoManager = $this->logoManager;

        $tempLogo = tempnam(sys_get_temp_dir(), 'logo');
        file_put_contents($tempLogo, 'fake-logo-content');

        $logoManager->setLogo($tempLogo);

        $this->assertEquals($tempLogo, $logoManager->getLogoPath());

        if (file_exists($tempLogo)) {
            unlink($tempLogo);
        }
    }

    /**
     * Test setting an invalid logo path throws an exception
     */
    #[Test]
    public function isSetInvalidLogoPathThrowsException(): void
    {
        $logoManager = $this->logoManager;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Logo Path '/invalid/path/logo.png' Does Not Exist Or Is Not Readable");

        $logoManager->setLogo('/invalid/path/logo.png');
    }

    /**
     * Test setting and getting the logo background setting
     */
    #[Test]
    public function isSetAndGetLogoBackground(): void
    {
        $logoManager = $this->logoManager;

        $logoManager->setLogoBackground(true);
        $this->assertTrue($logoManager->getLogoBackground());

        $logoManager->setLogoBackground(false);
        $this->assertFalse($logoManager->getLogoBackground());
    }

    /**
     * Test setting and getting the logo size
     */
    #[Test]
    public function isSetAndGetLogoSize(): void
    {
        $logoManager = $this->logoManager;

        $logoManager->setLogoSize(100);
        $logoSize = $logoManager->getLogoSize();

        $this->assertIsInt($logoSize);
        $this->assertEquals(100, $logoSize);
    }

    /**
     * Test setting an invalid logo size throws an exception
     */
    #[Test]
    public function isSetInvalidLogoSizeThrowsException(): void
    {
        $logoManager = $this->logoManager;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Logo Size Must Be A Positive Integer');

        $logoManager->setLogoSize(-10);
    }
}
