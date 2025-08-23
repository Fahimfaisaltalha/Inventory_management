<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    function dashboardPage():View{
        return view('pages.dashboard.dashboard-page');
    }
    
    public function summary(Request $request){
        $user_id = $request->header('user_id');

        $product = Product::where('user_id', $user_id)->count();
        $category = Category::where('user_id', $user_id)->count();
        $customer = Customer::where('user_id', $user_id)->count();
        $invoice = Invoice::where('user_id', $user_id)->count();
        
       
        $invoiceSummary = Invoice::where('user_id', $user_id)
        ->selectRaw('SUM(total) as total, SUM(vat) as vat, SUM(payable) as payable')
        ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'product' => $product,
                'category' => $category,
                'customer' => $customer,
                'invoice' => $invoice,
                'total' => $invoiceSummary->total ?? 0,
                'vat' => $invoiceSummary->vat ?? 0,
                'payable' => $invoiceSummary->payable ?? 0,
            ]
            ], 200);
    }
}
