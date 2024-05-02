<?php

namespace Tests\Unit\Services;

use App\Services\CheckoutService;
use PHPUnit\Framework\TestCase;

/**
 * Class CheckoutServiceTest
 * @package Tests\Unit\Services
 *
 * Test cases for the CheckoutService class.
 */
class CheckoutServiceTest extends TestCase
{
    /**
     * @var array $pricing An array containing pricing rules for different items.
     */
    private array $pricing;

    /**
     * Set up the pricing rules before each test method.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Define the pricing rules for the test cases
        $this->pricing = [
            'FR1' => ['price' => 3.11, 'discount' => 'BOGOF'],
            'SR1' => ['price' => 5.00, 'bulk_qty' => 3, 'bulk_price' => 4.50],
            'CF1' => ['price' => 11.23]
        ];
    }

    /**
     * Test case for calculating total price with a combination of items FR1, SR1, FR1, FR1, CF1.
     * The expected total price is £22.45.
     */
    public function testTotalCase1()
    {
        // Arrange: Create a new instance of CheckoutService with the defined pricing rules
        $service = new CheckoutService($this->pricing);

        // Act: Scan the specified items
        $service->scan('FR1');
        $service->scan('SR1');
        $service->scan('FR1');
        $service->scan('FR1');
        $service->scan('CF1');

        // Assert: Check that the calculated total matches the expected total
        $this->assertSame('22.45', $service->total());
    }

    /**
     * Test case for calculating total price with a combination of items FR1, FR1.
     * The expected total price is £3.11.
     */
    public function testTotalCase2()
    {
        // Arrange: Create a new instance of CheckoutService with the defined pricing rules
        $service = new CheckoutService($this->pricing);

        // Act: Scan the specified items
        $service->scan('FR1');
        $service->scan('FR1');

        // Assert: Check that the calculated total matches the expected total
        $this->assertSame('3.11', $service->total());
    }

    /**
     * Test case for calculating total price with a combination of items SR1, SR1, FR1, SR1.
     * The expected total price is £16.61.
     */
    public function testTotalCase3()
    {
        // Arrange: Create a new instance of CheckoutService with the defined pricing rules
        $service = new CheckoutService($this->pricing);

        // Act: Scan the specified items
        $service->scan('SR1');
        $service->scan('SR1');
        $service->scan('FR1');
        $service->scan('SR1');

        // Assert: Check that the calculated total matches the expected total
        $this->assertSame('16.61', $service->total());
    }
}
