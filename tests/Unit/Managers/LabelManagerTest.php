<?php

namespace HeroQR\Tests\Managers;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use HeroQR\Managers\LabelManager;
use HeroQR\Managers\ColorManager;
use PHPUnit\Framework\Attributes\Test;
use HeroQR\Contracts\Managers\LabelManagerInterface;

/**
 * Class LabelManagerTest
 * Test cases for LabelManager class.
 */
class LabelManagerTest extends TestCase
{
    private LabelManagerInterface $labelManager;

    /**
     * Set up an instance of LabelManager before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->labelManager = new LabelManager(new ColorManager());
    }

    #[Test]
    public function isSetAndGetLabel(): void
    {
        $label = $this->labelManager;

        $this->assertInstanceOf(LabelManagerInterface::class, $label, 'LabelManager Instance Is invalid');

        $label->setLabel('test label');
        $this->assertEquals('test label', $label->getLabel(), 'Label Text Does Not Match Expected Value');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Label Text Cannot Be Empty');
        $label->setLabel('');
    }

    #[Test]
    public function isInvalidLabelText(): void
    {
        $label = $this->labelManager;
        $longText = str_repeat('a', 201);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Label Text Cannot Exceed 200 Characters');
        $label->setLabel($longText);
    }

    #[Test]
    public function isGetLabelFont(): void
    {
        $label = $this->labelManager;

        $fontSize = $label->getLabelFont()->getSize();
        $this->assertIsInt($fontSize, 'Font Size Should Be An Integer');
        $this->assertEquals(20, $fontSize, 'Default Font Size Should Be 20');

        $fontClass = explode('\\', $label->getLabelFont()::class);
        $fontName = end($fontClass);
        $this->assertEquals('OpenSans', $fontName, 'Default Font Name Should Be OpenSans');
    }

    #[Test]
    public function isSetLabelSize(): void
    {
        $label = $this->labelManager;

        $label->setLabelSize(15);
        $this->assertEquals(15, $label->getLabelFont()->getSize(), 'Font size Should Be Updated To 15');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Font Size Must Be A Positive Integer');
        $label->setLabelSize(-10);
    }

    #[Test]
    public function isSetLabelAlign(): void
    {
        $label = $this->labelManager;

        $this->assertEquals('center', $label->getLabelAlign()->value, 'Default Alignment Should Be Center.');

        $validAlignments = ['right', 'left', 'CENTER'];
        foreach ($validAlignments as $alignment) {
            $label->setLabelAlign($alignment);
            $this->assertEquals(strtolower($alignment), $label->getLabelAlign()->value, "Alignment Should Be Set To {$alignment}");
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Label Alignment. Allowed Values Are "left", "center", or "right"');
        $label->setLabelAlign('invalid');
    }

    #[Test]
    public function isSetAndGetLabelColor(): void
    {
        $label = $this->labelManager;

        $defaultColor = $label->getLabelColor();
        $this->assertEquals([0, 0, 0], [$defaultColor->getRed(), $defaultColor->getGreen(), $defaultColor->getBlue()], 'Default label color should be black.');

        $label->setLabelColor('#FF573390');
        $customColor = $label->getLabelColor();
        $this->assertEquals([255, 87, 51, 72], [$customColor->getRed(), $customColor->getGreen(), $customColor->getBlue(), $customColor->getAlpha()], 'Custom color values do not match expected.');
    }

    #[Test]
    public function isInvalidLabelColor(): void
    {
        $label = $this->labelManager;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Label Color Format');
        $label->setLabelColor('#fffffff');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Label Color Format');
        $label->setLabelColor('ffffff');
    }

    #[Test]
    public function isSetAndGetLabelMargin(): void
    {
        $label = $this->labelManager;

        $defaultMargin = $label->getLabelMargin();
        $this->assertEquals([0, 10, 10, 10], [$defaultMargin->getTop(), $defaultMargin->getRight(), $defaultMargin->getBottom(), $defaultMargin->getLeft()], 'Default margins do not match expected values.');

        $label->setLabelMargin([5, 5, 5, 5]);
        $customMargin = $label->getLabelMargin();
        $this->assertEquals([5, 5, 5, 5], [$customMargin->getTop(), $customMargin->getRight(), $customMargin->getBottom(), $customMargin->getLeft()], 'Custom margins do not match expected values.');
    }

    #[Test]
    public function isInvalidLabelMargin(): void
    {
        $label = $this->labelManager;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Margin Array Must Contain Exactly 4 Values [top, right, bottom, left]');
        $label->setLabelMargin([10, 0]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Margin values must be between -200 and 200.');
        $label->setLabelMargin([-302, 0, 210, 0]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All Margin Values Must Be Numeric');
        $label->setLabelMargin(['one', '0', '5.2', '5']);
    }
}
