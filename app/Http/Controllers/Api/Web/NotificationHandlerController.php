<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Product;

class NotificationHandlerController extends Controller
{
    /**
     *
     * index
     */

    public function index(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);
        $validSignatureKey = hash("sha256", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));

        if($notification->signature_key != $validSignatureKey) {
          return response(['message' => 'Invalid signature'], 403);
        }

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;

        // data transaction
        $data_transaction = Invoice::where('invoice', $orderId)->first();

        if ($transaction == 'capture') {
           if ($type == 'credit_card') {

           }
        } else if($transaction == 'settlement') {
            /**
             * update invoice to success
             */

            $data_transaction->update([
               'status' => 'success'
            ]);

            // update stock product
            foreach ($data_transaction->orders()->get() as $order) {
                $product = Product::whereId($order->product_id)->first();
                $product->update([
                    'stock' => $product->stock - $order->qty
                ]);
            }
        } else if ($transaction == 'pending') {
            /**
             *
             * update invoice to pending
             */

            $data_transaction->update([
               'status' => 'pending'
            ]);
        } else if ($transaction == 'deny') {
            /**
             *
             * update invoice to failed
             */

            $data_transaction->update([
               'status' => 'failed'
            ]);
        } else if ($transaction == 'expire') {
            /**
             *
             * update invoice to expired
             */

            $data_transaction->update([
               'status' => 'expired'
            ]);
        } else if ($transaction == 'cancel') {
            /**
             *
             * update invoice to failed
             *
             */
            $data_transaction->update([
                'status' => 'failed'
            ]);
        }
    }
}
