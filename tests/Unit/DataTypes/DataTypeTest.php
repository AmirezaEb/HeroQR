<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\DataType;
use PHPUnit\Framework\{Attributes\Test,TestCase};

/**
 * Class DataTypeTest
 * Tests the DataType class.
 */
class DataTypeTest extends TestCase
{
    /**
     * Test to ensure all values in the DataType enum are valid classes
     */
    #[Test]
    public function isEnumValuesAreValidClasses(): void
    {
        foreach (DataType::cases() as $case) {
            $class = $case->value;
            $this->assertTrue(class_exists($class), "Class $class does not exist");
        }
    }
}