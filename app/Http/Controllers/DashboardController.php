<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Buyer;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $data = [];

            if ($user->hasRole('admin')) {
                $data = [
                    'total_revenue' => (float) Transaction::where('payment_status', 'paid')->sum('grand_total'),
                    'total_stores' => Store::count(),
                    'total_buyers' => Buyer::count(),
                    'total_products' => Product::count(),
                    'total_transactions' => Transaction::count(),
                ];
            } elseif ($user->hasRole('store')) {
                $store = $user->store;
                
                if ($store) {
                    $data = [
                        'total_revenue' => (float) Transaction::where('store_id', $store->id)->where('payment_status', 'paid')->sum('grand_total'),
                        'total_products' => Product::where('store_id', $store->id)->count(),
                        'total_buyers' => Transaction::where('store_id', $store->id)->where('payment_status', 'paid')->distinct('buyer_id')->count('buyer_id'),
                        'total_transactions' => Transaction::where('store_id', $store->id)->count(),
                    ];
                } else {
                    $data = [
                        'total_revenue' => 0.0,
                        'total_products' => 0,
                        'total_buyers' => 0,
                        'total_transactions' => 0,
                    ];
                }
            } elseif ($user->hasRole('buyer')) {
                $buyer = $user->buyer;

                if ($buyer) {
                    $data = [
                        'total_expenses' => (float) Transaction::where('buyer_id', $buyer->id)->where('payment_status', 'paid')->sum('grand_total'),
                        'total_products' => TransactionDetail::whereHas('transaction', function ($q) use ($buyer) {
                            $q->where('buyer_id', $buyer->id)->where('payment_status', 'paid');
                        })->distinct('product_id')->count('product_id'),
                        'total_transactions' => Transaction::where('buyer_id', $buyer->id)->count(),
                    ];
                } else {
                    $data = [
                        'total_expenses' => 0.0,
                        'total_products' => 0,
                        'total_transactions' => 0,
                    ];
                }
            }

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
