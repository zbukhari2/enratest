<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckoutService;

/**
 * Class CheckoutController
 *
 * Controller responsible for managing checkout operations.
 */
class CheckoutController extends Controller
{
    /**
     * Process checkout for scanned items.
     *
     * @param Request $request The HTTP request containing scanned items.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the total price.
     */
    public function checkout(Request $request)
    {
        // Assuming $request->items is an array of scanned items
        $items = $request->items;

        $pricing = [
            'FR1' => ['price' => 3.11, 'discount' => 'BOGOF'],
            'SR1' => ['price' => 5.00, 'bulk_qty' => 3, 'bulk_price' => 4.50],
            'CF1' => ['price' => 11.23]
        ];


        // Initialize the checkout service
        $checkoutService = new CheckoutService($pricing);

        // Scan each item
        foreach ($items as $item) {
            $checkoutService->scan($item);
        }

        // Calculate the total price
        $totalPrice = $checkoutService->total();

        // Return the total price as JSON response
        return response()->json(['total_price' => $totalPrice]);
    }
}
