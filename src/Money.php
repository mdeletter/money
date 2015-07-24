<?php

namespace Money;

/**
 * Class Money
 */
class Money
{
    const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    /**
     * Amount represented as integer.
     * @var int
     */
    private $amount;

    /**
     * Number of decimals or precision.
     * @var int
     */
    private $precision;

    /**
     * Decimal precision calculating value.
     * @var int
     */
    private $precisionCalc;

    /**
     * Create a Money instance.
     *
     * @param float $amount
     *   Amount in Euros
     * @param int $precision
     *   Decimal precision of the Money object
     */
    public function __construct($amount, $precision = 2)
    {
        $this->precision = $precision;
        $this->precisionCalc = 10 ** $precision;
        $this->amount = (int) round($amount * $this->precisionCalc);
    }

    /**
     * Copy the current Money instance.
     * @return Money
     *   New object
     */
    public function copy()
    {
        return new self($this->getAmount(), $this->precision);
    }

    /**
     * Change the decimal precision of the Money object.
     *
     * @param int $precision
     *   Decimal precision of the Money object
     */
    public function changePrecision($precision)
    {
        $this->precision = $precision;
        $calc = 10 ^ $precision;
        $this->amount = (int) round($this->getAmount() * $calc);
        $this->precisionCalc = $calc;
    }

    /**
     * Checks if equal.
     *
     * @param Money $other
     *   Other Money object
     *
     * @return bool
     *   True if equal, else false
     */
    public function equals(Money $other)
    {
        return $this->amount == $other->amount;
    }

    /**
     * Compare to other Money object.
     *
     * @param Money $other
     *   Other Money object
     *
     * @return int
     *   -1 if smaller, 0 if equal and 1 if bigger
     */
    public function compare(Money $other)
    {
        if ($this->amount < $other->amount) {
            return -1;
        } elseif ($this->amount == $other->amount) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Check if this is greater than an other Money object.
     *
     * @param Money $other
     *   Other Money object
     *
     * @return bool
     *   True if greater, else false
     */
    public function greaterThan(Money $other)
    {
        return 1 == $this->compare($other);
    }

    /**
     * Check if this is greater than or equal to an other Money object.
     *
     * @param Money $other
     *   Other Money object
     *
     * @return bool
     *   True if greater or equal, else false
     */
    public function greaterThanOrEqual(Money $other)
    {
        return 0 <= $this->compare($other);
    }

    /**
     * Check if this is less than an other Money object.
     *
     * @param Money $other
     *   Other Money object
     *
     * @return bool
     *   True if less, else false
     */
    public function lessThan(Money $other)
    {
        return -1 == $this->compare($other);
    }

    /**
     * Check if this is less than or equal to an other Money object.
     *
     * @param Money $other
     *   Other Money object
     *
     * @return bool
     *   True if less or equal, else false
     */
    public function lessThanOrEqual(Money $other)
    {
        return 0 >= $this->compare($other);
    }

    /**
     * Return the amount.
     * @return float
     *   Amount
     */
    public function getAmount()
    {
        return (float) $this->amount / $this->precisionCalc;
    }

    /**
     * Add the addend Money object to this.
     *
     * @param Money $addend
     *   Added Money object
     *
     * @return Money
     *   New Money object
     */
    public function add(Money $addend)
    {
        $this->amount += $addend->amount;
        return $this;
    }

    /**
     * Subtract the subtrahend Money object from this.
     *
     * @param Money $subtrahend
     *   Subtracted Money object
     *
     * @return Money
     *   New Money object
     */
    public function subtract(Money $subtrahend)
    {
        $this->amount -= $subtrahend->amount;
        return $this;
    }

    /**
     * Multiply this object with the multiplier.
     *
     * @param int /float $multiplier
     *   Multiplier
     * @param int $rounding_mode
     *   Rounding mode
     *
     * @return Money
     *   New Money object
     */
    public function multiply($multiplier, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->amount = (int) round($this->amount * $multiplier, 0, $rounding_mode);
        return $this;
    }

    /**
     * Divide this with the divisor.
     *
     * @param int /float $divisor
     *   Divisor
     * @param int $rounding_mode
     *   Rounding mode
     *
     * @throws InvalidArgumentException
     * @return Money
     *   New Money object
     */
    public function divide($divisor, $rounding_mode = self::ROUND_HALF_UP)
    {
        if ($divisor === 0) {
            throw new InvalidArgumentException('Division by zero');
        } elseif ($divisor < 1 / $this->precisionCalc) {
            throw new InvalidArgumentException('Divisor to small!');
        }
        $this->amount = (int) round($this->amount / $divisor, 0, $rounding_mode);
        return $this;
    }

    /**
     * Allocate the money according to a list of ratio's.
     *
     * @param array $ratios
     *   List of int/float ratio's
     *
     * @return array
     *   Money object array
     */
    public function allocate(array $ratios)
    {
        $remainder = $this->amount;
        $results = [];
        $total = array_sum($ratios);
        foreach ($ratios as $ratio) {
            $share = round($this->amount * $ratio / $total);
            $results[] = new Money($share / $this->precisionCalc, $this->precision);
            $remainder -= $share;
        }

        // Edge case, when two or more values are exactly half.
        if ($remainder < 0) {
            $sort = $results;
            usort($sort, function (Money $a, Money $b) {
                return $b->compare($a);
            });
            for ($i = 0; $remainder < 0; $i++) {
                if ($sort[$i]->amount === 0) {
                    continue;
                }
                $sort[$i]->amount--;
                $remainder++;
            }
        } elseif ($remainder > 0) {
            $sort = $results;
            usort($sort, function (Money $a, Money $b) {
                return $a->compare($b);
            });
            for ($i = 0; $remainder > 0; $i++) {
                if ($sort[$i]->amount === 0) {
                    continue;
                }
                $sort[$i]->amount++;
                $remainder--;
            }
        }
        return $results;
    }

    /**
     * Check if zero.
     * @return bool
     *   True if 0, else false.
     */
    public function isZero()
    {
        return $this->amount === 0;
    }

    /**
     * Check if positive.
     * @return bool
     *   True if positive, else false
     */
    public function isPositive()
    {
        return $this->amount > 0;
    }

    /**
     * Check if negative.
     * @return bool
     *   True if negative, else false
     */
    public function isNegative()
    {
        return $this->amount < 0;
    }
}
