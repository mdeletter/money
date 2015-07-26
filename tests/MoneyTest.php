<?php

namespace Money;

/**
 * Class MoneyTest
 */
class MoneyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test all the basic functionality.
     */
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

    /**
     * Test changing the precision.
     */
    public function testChangePrecision()
    {
        $a = new Money(12);

        /**
         * Test increasing precision.
         */
        $a->changePrecision(3);
        $this->assertEqualFloats($a->getAmount(), 12);

        /**
         * Test decreasing precision
         */
        $a->changePrecision(-1);
        $this->assertEqualFloats($a->getAmount(), 10);

        /**
         * Test decreasing precision in such a manner that it will be zero.
         */
        $a->changePrecision(-2);
        $this->assertEqualFloats($a->getAmount(), 0);
    }

    /**
     * Test multiplication.
     */
    public function testMultiply()
    {
        $a = new Money(1);
        $a->multiply(10);
        $this->assertEqualFloats($a->getAmount(), 10);
    }

    /**
     * Test division.
     */
    public function testDivide()
    {
        $a = new Money(1);
        $a->divide(10);
        $this->assertEqualFloats($a->getAmount(), 0.1);
    }

    /**
     * Test is zero check.
     */
    public function testIsZero()
    {
        $a = new Money(0);
        $b = new Money(1);
        $c = new Money(-1);

        $this->assertTrue($a->isZero());
        $this->assertTrue(!$b->isZero());
        $this->assertTrue(!$c->isZero());
    }

    /**
     * Test is positive check.
     */
    public function testIsPositive()
    {
        $a = new Money(0);
        $b = new Money(1);
        $c = new Money(-1);

        $this->assertTrue(!$a->isPositive());
        $this->assertTrue($b->isPositive());
        $this->assertTrue(!$c->isPositive());
    }

    /**
     * Test is negative check.
     */
    public function testIsNegative()
    {
        $a = new Money(0);
        $b = new Money(1);
        $c = new Money(-1);

        $this->assertTrue(!$a->isNegative());
        $this->assertTrue(!$b->isNegative());
        $this->assertTrue($c->isNegative());
    }

    /**
     * Test the allocate function.
     */
    public function testAllocate()
    {
        $a = new Money(0.10);

        /**
         * Test fair share.
         */
        $result = $a->allocate([1,1]);
        $this->assertEqualFloats($result[0]->getAmount(), 0.05);
        $this->assertEqualFloats($result[1]->getAmount(), 0.05);

        /**
         * Test edge case.
         */
        $result = $a->allocate([3, 2, 1]);
        $this->assertEqualFloats($result[0]->getAmount(), 0.05);
        $this->assertEqualFloats($result[1]->getAmount(), 0.03);
        $this->assertEqualFloats($result[2]->getAmount(), 0.02);

        /**
         * Test same edge case, but different order.
         */
        $result = $a->allocate([2, 3, 1]);
        $this->assertEqualFloats($result[0]->getAmount(), 0.03);
        $this->assertEqualFloats($result[1]->getAmount(), 0.05);
        $this->assertEqualFloats($result[2]->getAmount(), 0.02);

        /**
         * Test edge case all get te same, but there is not enough.
         */
        $result = $a->allocate([1, 1, 1, 1]);
        $this->assertEqualFloats($result[0]->getAmount(), 0.03);
        $this->assertEqualFloats($result[1]->getAmount(), 0.03);
        $this->assertEqualFloats($result[2]->getAmount(), 0.02);
        $this->assertEqualFloats($result[3]->getAmount(), 0.02);

        $b = new Money(-0.10);

        /**
         * Test fair share negative.
         */
        $result = $b->allocate([1,1]);
        $this->assertEqualFloats($result[0]->getAmount(), -0.05);
        $this->assertEqualFloats($result[1]->getAmount(), -0.05);

        /**
         * Test edge case negative.
         */
        $result = $b->allocate([3, 2, 1]);
        $this->assertEqualFloats($result[0]->getAmount(), -0.05);
        $this->assertEqualFloats($result[1]->getAmount(), -0.03);
        $this->assertEqualFloats($result[2]->getAmount(), -0.02);

        /**
         * Test edge case negative, but different order.
         */
        $result = $b->allocate([2, 3, 1]);
        $this->assertEqualFloats($result[0]->getAmount(), -0.03);
        $this->assertEqualFloats($result[1]->getAmount(), -0.05);
        $this->assertEqualFloats($result[2]->getAmount(), -0.02);

        /**
         * Test edge case negative all get the same, but there is not enough.
         */
        $result = $b->allocate([1, 1, 1, 1]);
        $this->assertEqualFloats($result[0]->getAmount(), -0.03);
        $this->assertEqualFloats($result[1]->getAmount(), -0.03);
        $this->assertEqualFloats($result[2]->getAmount(), -0.02);
        $this->assertEqualFloats($result[3]->getAmount(), -0.02);
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
