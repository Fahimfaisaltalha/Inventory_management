<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    function ReportPage(){
        return view('pages.dashboard.report-page');
    }

    

    public function salesReport(Request $request)
    {
    $user_id = $request->header('user_id');
    $from_date = date('Y-m-d', strtotime($request->FormDate));
    $to_date = date('Y-m-d', strtotime($request->ToDate));

    // dd($from_date);
    
    $invoices = Invoice::where('user_id', $user_id)
        ->whereDate('created_at', '>=', $from_date)
        ->whereDate('created_at', '<=', $to_date)
        ->with('customer')
        ->get();

    $total = $invoices->sum('total');
    $vat = $invoices->sum('vat');
    $payable = $invoices->sum('payable');
    $discount = $invoices->sum('discount');

    $data = [
        'total' => $total,
        'vat' => $vat,
        'payable' => $payable,
        'discount' => $discount,
        'list' => $invoices,
        'from_date' => $from_date,
        'to_date' => $to_date
    ];

    $pdf = Pdf::loadView('report.sales_report', $data);

    return $pdf->download('sales_report.pdf');
}
}