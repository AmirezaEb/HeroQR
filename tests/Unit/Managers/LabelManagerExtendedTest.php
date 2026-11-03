<?php

namespace HeroQR\Tests\Unit\Managers;

use HeroQR\{Contracts\Managers\LabelManagerInterface, Managers\ColorManager, Managers\LabelManager};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class LabelManagerExtendedTest
 * Extended coverage for LabelManager edge cases and escaping.
 */
class LabelManagerExtendedTest extends TestCase
{
    private LabelManager $labelManager;

    /**
     * Initializes the LabelManager instance
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->labelManager = new LabelManager(new ColorManager());
        $this->assertInstanceOf(LabelManagerInterface::class, $this->labelManager);
    }

    /**
     * Test that HTML special characters in label text are escaped
     */
    #[Test]
    public function isLabelHtmlIsEscaped(): void
    {
        $this->labelManager->setLabel('<b>HeroQR</b>');
        $this->assertSame('&lt;b&gt;HeroQR&lt;/b&gt;', $this->labelManager->getLabel());
    }

    /**
     * Test label with exactly 200 characters is accepted
     */
    #[Test]
    public function isLabelWithExactMaxLengthAccepted(): void
    {
        $label = str_repeat('a', 200);
        $this->labelManager->setLabel($label);
        $this->assertSame($label, $this->labelManager->getLabel());
    }

    /**
     * Test default label text is empty
     */
    #[Test]
    public function isDefaultLabelEmpty(): void
    {
        $this->assertSame('', $this->labelManager->getLabel());
    }

    /**
     * Provides valid and boundary margin arrays
     */
    public static function validMarginProvider(): array
    {
        return [
            'zeros' => [[0, 0, 0, 0]],
            'mixed' => [[10, -10, 20, -20]],
            'upper boundary' => [[250, 250, 250, 250]],
            'lower boundary' => [[-250, -250, -250, -250]],
        ];
    }

    /**
     * Test valid margin arrays are accepted
     */
    #[Test]
    #[DataProvider('validMarginProvider')]
    public function isSetValidLabelMargin(array $margin): void
    {
        $this->labelManager->setLabelMargin($margin);
        $customMargin = $this->labelManager->getLabelMargin();

        $this->assertEquals($margin, [
            $customMargin->getTop(),
            $customMargin->getRight(),
            $customMargin->getBottom(),
            $customMargin->getLeft(),
        ]);
    }

    /**
     * Test margin value above allowed range throws exception
     */
    #[Test]
    public function isMarginAboveRangeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Must Be Between -250 And 250');

        $this->labelManager->setLabelMargin([251, 0, 0, 0]);
    }

    /**
     * Test non-numeric margin value throws exception
     */
    #[Test]
    public function isNonNumericMarginThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Must Be Numeric Value');

        $this->labelManager->setLabelMargin(['top', 0, 0, 0]);
    }

    /**
     * Test setting large valid font size
     */
    #[Test]
    public function isSetLargeLabelSizeValid(): void
    {
        $this->labelManager->setLabelSize(100);
        $this->assertSame(100, $this->labelManager->getLabelFont()->getSize());
    }

    /**
     * Test label size zero throws exception
     */
    #[Test]
    public function isSetLabelSizeZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Font Size Must Be A Positive Integer');

        $this->labelManager->setLabelSize(0);
    }
}
