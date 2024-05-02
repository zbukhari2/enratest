<?php

namespace App\Services;

/**
 * Class CheckoutService
 *
 * Represents a checkout service for processing items and calculating total prices.
 */
class CheckoutService
{
    /**
     * @var array $pricing An array containing pricing rules for different items.
     */
    private array $pricing;

    /**
     * @var array $basket An array representing the scanned items in the checkout basket.
     */
    private array $basket;

    /**
     * @var float $totalPrice The total price of all scanned items in the basket.
     */
    private float $totalPrice;

    /**
     * CheckoutService constructor.
     *
     * Initializes the CheckoutService with default pricing rules and an empty basket.
     */
    public function __construct(array $pricing)
    {
        $this->pricing = $pricing;
        $this->basket = [];
        $this->totalPrice = 0.00;
    }

    /**
     * Add an item to the basket.
     *
     * @param string $itemCode The code of the item to be scanned.
     *
     * @throws \InvalidArgumentException If the provided item code is invalid.
     */
    public function scan(string $itemCode): void
    {
        if (!isset($this->pricing[$itemCode])) {
            throw new \InvalidArgumentException("Invalid item code: $itemCode");
        }
        $this->basket[] = $itemCode;
    }

    /**
     * Calculate the total price of all scanned items in the basket.
     *
     * @return string The total price formatted as a string with two decimal places.
     */
    public function total(): string
    {
        $this->totalPrice = 0.00;

        // Calculate the total price of items without considering discounts
        foreach ($this->basket as $itemCode) {
            $this->totalPrice += $this->pricing[$itemCode]['price'];
        }

        // Apply discounts based on pricing rules
        foreach ($this->pricing as $itemCode => $rule) {
            if (isset($rule['discount']) && $rule['discount'] === 'BOGOF') {
                $this->applyBogofDiscount($itemCode);
            } elseif (isset($rule['bulk_qty']) && isset($rule['bulk_price'])) {
                $this->applyQuantityDiscount($itemCode);
            }
        }

        return number_format($this->totalPrice, 2);
    }

    /**
     * Apply buy-one-get-one-free discount for the given item.
     *
     * @param string $itemCode The code of the item to which the discount is applied.
     */
    private function applyBogofDiscount(string $itemCode): void
    {
        $count = array_count_values($this->basket)[$itemCode] ?? 0;
        $discountedItems = intdiv($count, 2);

        $this->totalPrice -= $discountedItems * $this->pricing[$itemCode]['price'];
    }

    /**
     * Apply quantity discount for the given item.
     *
     * @param string $itemCode The code of the item to which the discount is applied.
     */
    private function applyQuantityDiscount(string $itemCode): void
    {
        $count = array_count_values($this->basket)[$itemCode] ?? 0;
        if ($count >= $this->pricing[$itemCode]['bulk_qty']) {
            $this->totalPrice -= $count * ($this->pricing[$itemCode]['price'] - $this->pricing[$itemCode]['bulk_price']);
        }
    }
}
