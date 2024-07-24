<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Section;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.reports');
    }
    public function indexcustomers()
    {
        $sections = Section::all();
        return view('reports.customers',[
            'sections'=> $sections,
        ]);
    }
    
    public function searchInvoice(Request $request)
    {

        $start_at = date($request->start_at);
        $end_at = date($request->end_at);


        if ($request->rdio == 1) {

            $details = $request->start_at && $request->end_at ?
                Invoice::whereBetween('invoice_Date', [$start_at, $end_at])->where('Status', $request->type)->get()
                :
                Invoice::where('Status', $request->type)->get();

            return view('reports.reports', [
                'details' => $details,
                'type' => $request->type,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
            ]);
        } else {
            $details = Invoice::where('invoice_number', $request->invoice_number)->get();
            return view('reports.reports', ['details' => $details]);
        }
    }

    public function searchCustomer(Request $request)
    {
       // return $request;

        $start_at = date($request->start_at);
        $end_at = date($request->end_at);      

            $details = $request->start_at && $request->end_at ?
                Invoice::whereBetween('invoice_Date', [$start_at, $end_at])->where('section_id', $request->Section)->get()
                :
                Invoice::where('section_id', $request->Section)->get();
        $sections = Section::all();
            return view('reports.customers', [
                'details' => $details,
            'sections' => $sections,
              
            ]);
        
    }
}
