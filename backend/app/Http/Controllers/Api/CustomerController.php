<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * GET /api/customers/{id}
     * Returns customer details including their order history.
     */
    public function show(string $id)
    {
        $customer = Customer::with(['orders.items.medication'])->findOrFail($id);

        return response()->json($customer);
    }
}
