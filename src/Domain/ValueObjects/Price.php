<?php

namespace App\Domain\ValueObjects;

class Price
{
    private float $amount;

    public function __construct(float $amount)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Price cannot be negative");
        }
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function format(string $currency = 'VND'): string
    {
        return number_format($this->amount, 0) . ' ' . $currency;
    }

    public function equals(Price $other): bool
    {
        return $this->amount === $other->amount;
    }

    public function greaterThan(Price $other): bool
    {
        return $this->amount > $other->amount;
    }

    public function lessThan(Price $other): bool
    {
        return $this->amount < $other->amount;
    }
}

