<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     *
     * Display a listing of the resource
     */

    public function index()
    {
        $invoices = Invoice::latest()->when(request()->q, function($invoices) {
           $invoices = $invoices->where('invoice', 'like', '%'.request()->q, '%');
        })->where('customer_id', auth()->guard('api_customer')->user()->id)->paginate(5);

        // return with Api Resource
        return new InvoiceResource(true, 'List Data Invoices : '.auth()->guard('api_customer')->user()->name.'', $invoices);
    }

    /**
     *
     * Display the specific resource.
     */

    public function show($snap_token)
    {
        $invoice = Invoice::with('orders.product', 'customer', 'city', 'province')->where('customer_id', auth()->guard('api_customer')->user()->id)->where('snap_token', $snap_token)->first();

        if(!$invoice) {
            return new InvoiceResource(false, 'Detail data invoice tidak ditemukan!', null);
        }

        return new InvoiceResource(true, 'Detail Data Invoice : '.$invoice->snap_token.'', $invoice);
    }
}
