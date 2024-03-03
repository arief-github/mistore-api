<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
class CartController extends Controller
{
    /**
     *
     * __construct
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api_customer');
    }
    /**
     *
     * Display a listing of the resource
     *
     */
    public function index()
    {
        $carts = Cart::with('product')->where('customer_id', auth()->guard('api_customer')->user()->id)->latest()->get();

        // return with api resource
        return new CartResource(true, 'List Data Carts : '.auth()->guard('api_customer')->user()->name.'',$carts);
    }
    /**
     *
     * store a newly created
     */

    public function store(Request $request)
    {
        $item = Cart::where('product_id', $request->product_id)->where('customer_id', auth()->guard('api_customer')->user()->id);

        // check if product already in cart and then increment qty
        if($item->count()) {
            // increment / update qty
            $item->increment('qty');

            $item = $item->first();

            // sum price * quantity
            $price = $request->price * $item->qty;

            // sum weight
            $weight = $request->weight * $item->qty;

            $item->update([
                'price' => $price,
                'weight' => $weight
            ]);
        } else {
            // insert new item into cart
            $item = Cart::create([
                'product_id' => $request->product_id,
                'customer_id' => auth()->guard('api_customer')->user()->id,
                'qty' => $request->qty,
                'price' => $request->price,
                'weight' => $request->weight
            ]);
        }

        return new CartResource(true, 'Sukses menambahkan item kedalam cart', $item);
    }

    /**
     * getCartPrice
     *
     */

    public function getCartPrice()
    {
        $totalPrice = Cart::with('product')->where('customer_id', auth()->guard('api_customer')->user()->id)->sum('price');

        return new CartResource(true, 'Total Harga', $totalPrice);
    }

    /**
     *
     * getWeightCart
     *
     */
    public function getCartWeight()
    {
        $totalWeight = Cart::with('product')->where('customer_id', auth()->guard('api_customer')->user()->id)->sum('weight');

        // return with Api Resource
        return new CartResource(true, 'Total Berat Bersih', $totalWeight);
    }

    /**
     *
     * Remove item from cart
     */

    public function removeCart(Request $request)
    {
        $cart = Cart::with('product')->whereId($request->cart_id)->first();

        $cart->delete();

        return new CartResource(true, 'Success Remove Item from Cart', null);
    }

}


