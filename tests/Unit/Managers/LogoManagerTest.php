<?php

namespace HeroQR\Tests\Managers;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use HeroQR\Managers\LogoManager;
use PHPUnit\Framework\Attributes\Test;
use HeroQR\Contracts\Managers\LogoManagerInterface;

/**
 * Class LogoManagerTest
 * Tests the LogoManager class.
 */
final class LogoManagerTest extends TestCase
{
    private LogoManagerInterface $logoManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logoManager = new LogoManager();
    }

    /**
     * Test setting and getting the logo path with a valid file.
     */
    #[Test]
    public function isSetAndGetLogoPath()
    {
        $logoManager = $this->logoManager;


        $tempLogo = tempnam(sys_get_temp_dir(), 'logo');
        file_put_contents($tempLogo, 'fake-logo-content');

        $logoManager->setLogo($tempLogo);

        $this->assertInstanceOf(LogoManagerInterface::class, $logoManager);
        $this->assertEquals($tempLogo, $logoManager->getLogoPath());

        unlink($tempLogo);
    }

    /**
     * Test setting an invalid logo path throws an exception.
     */
    #[Test]
    public function isSetInvalidLogoPathThrowsException()
    {
        $logoManager = $this->logoManager;

        $this->assertInstanceOf(LogoManagerInterface::class, $logoManager);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Logo Path '/invalid/path/logo.png' Does Not Exist Or Is Not Readable");

        $logoManager->setLogo('/invalid/path/logo.png');
    }

    /**
     * Test setting and getting the logo background setting.
     */
    #[Test]
    public function isSetAndGetLogoBackground()
    {
        $logoManager = $this->logoManager;

        $logoManager->setLogoBackground(true);
        $this->assertInstanceOf(LogoManagerInterface::class, $logoManager);
        $this->assertTrue($logoManager->getLogoBackground());

        $logoManager->setLogoBackground(false);
        $this->assertFalse($logoManager->getLogoBackground());
    }

    /**
     * Test setting and getting the logo size.
     */
    #[Test]
    public function isSetAndGetLogoSize()
    {
        $logoManager = $this->logoManager;

        $logoManager->setLogoSize(100);
        $this->assertInstanceOf(LogoManagerInterface::class, $logoManager);
        $this->assertEquals(100, $logoManager->getLogoSize());
    }

    /**
     * Test setting an invalid logo size throws an exception.
     */
    #[Test]
    public function isSetInvalidLogoSizeThrowsException()
    {
        $logoManager = $this->logoManager;

        $this->assertInstanceOf(LogoManagerInterface::class, $logoManager);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Logo Size Must Be A Positive Integer');

        $logoManager->setLogoSize(-10);
    }
}
