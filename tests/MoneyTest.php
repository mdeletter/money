<?php

namespace Money;

/**
 * Class MoneyTest
 */
class MoneyTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleStuff()
    {
        $a = new Money(10);
        $b = $a->copy();
        $c = new Money(1);

        $small = $a->copy()->subtract($c);
        $big = $a->copy()->add($c);

        $this->assertEqualFloats($a->getAmount(), 10);

        $this->assertEqualFloats($a->getAmount(), $b->getAmount());
        $this->assertTrue($a->equals($b));
        $this->assertTrue($a->compare($b) === 0);
        $this->assertTrue($a->greaterThanOrEqual($b));

        $this->assertTrue($a->compare($small) === 1);
        $this->assertTrue($a->greaterThanOrEqual($small));
        $this->assertTrue($a->greaterThan($small));

        $this->assertTrue($a->compare($big) === -1);
        $this->assertTrue($a->lessThanOrEqual($big));
        $this->assertTrue($a->lessThan($big));
    }

    public function testChangePrecision()
    {
        $a = new Money(10);
        $a->changePrecision(3);
        $this->assertEqualFloats($a->getAmount(), 10);

        $a->changePrecision(-1);
        $this->assertEqualFloats($a->getAmount(), 10);
    }

    public function testMultiply()
    {
        $a = new Money(1);
        $a->multiply(10);
        $this->assertEqualFloats($a->getAmount(), 10);
    }

    public function testDivide()
    {
        $a = new Money(1);
        $a->divide(10);
        $this->assertEqualFloats($a->getAmount(), 0.1);
    }

    public function testIsZero()
    {
        $a = new Money(0);
        $b = new Money(1);
        $c = new Money(-1);

        $this->assertTrue($a->isZero());
        $this->assertTrue(!$b->isZero());
        $this->assertTrue(!$c->isZero());
    }

    public function testIsPositive()
    {
        $a = new Money(0);
        $b = new Money(1);
        $c = new Money(-1);

        $this->assertTrue(!$a->isPositive());
        $this->assertTrue($b->isPositive());
        $this->assertTrue(!$c->isPositive());
    }

    public function testIsNegative()
    {
        $a = new Money(0);
        $b = new Money(1);
        $c = new Money(-1);

        $this->assertTrue(!$a->isNegative());
        $this->assertTrue(!$b->isNegative());
        $this->assertTrue($c->isNegative());
    }

    public function testAllocate()
    {
        $a = new Money(0.10);
        $result = $a->allocate([1,1]);
        $this->assertEqualFloats($result[0]->getAmount(), 0.05);
        $this->assertEqualFloats($result[1]->getAmount(), 0.05);

        $result = $a->allocate([3, 2, 1]);
        $this->assertEqualFloats($result[0]->getAmount(), 0.05);
        $this->assertEqualFloats($result[1]->getAmount(), 0.03);
        $this->assertEqualFloats($result[2]->getAmount(), 0.02);

        $b = new Money(-0.10);
        $result = $b->allocate([1,1]);
        $this->assertEqualFloats($result[0]->getAmount(), -0.05);
        $this->assertEqualFloats($result[1]->getAmount(), -0.05);

        $result = $b->allocate([3, 2, 1]);
        $this->assertEqualFloats($result[0]->getAmount(), -0.05);
        $this->assertEqualFloats($result[1]->getAmount(), -0.03);
        $this->assertEqualFloats($result[2]->getAmount(), -0.02);
    }

    /**
     * Asserts if two floats are equal.
     *
     * @param float $a
     * @param float $b
     */
    protected function assertEqualFloats($a, $b)
    {
        $this->assertTrue(abs($a - $b) < 0.00000001);
    }
}
