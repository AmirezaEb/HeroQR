<?php 

namespace HeroQR\Tests\DataTypes;

use HeroQR\DataTypes\DataType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

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
            $this->assertTrue(class_exists($class), 'Class $class does not exist');
        }
    }
}

