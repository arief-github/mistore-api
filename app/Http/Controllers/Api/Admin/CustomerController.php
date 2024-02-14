<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display all customers
     */

    public function index()
    {
        $customers = Customer::when(request()->q, function($customers) {
           $customers = $customers->where('name', 'like', '%'.request()->q.'%');
        })->latest()->paginate(5);

        // return with ApiResource
        return new CustomerResource(true, 'List Data Customer', $customers);
    }
}
