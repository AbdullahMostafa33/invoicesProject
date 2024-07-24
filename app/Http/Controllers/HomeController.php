<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        return view('index');
    }
   public function home () {
    $all = Invoice::sum('Total');
    $paid= Invoice::Where('Value_Status',2)->sum('Total');
    $unpaid = Invoice::Where('Value_Status', 1)->sum('Total');
    $partpaid = Invoice::Where('Value_Status', 3)->sum('Total');
    $p_paid=round(($paid/$all)*100);
    $p_unpaid = round(($unpaid / $all) * 100);
    $p_part=($partpaid / $all) * 100;
     $chartjs = app()->chartjs
        ->name('barChartTest')
        ->type('bar')
        ->size(['width' => 400, 'height' => 200])
        ->labels(['الفواتير الغير مدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
        ->datasets([
            [
                "label" => "",
                'backgroundColor' => ['rgba(255, 99, 132, 0.3)', 'rgba(54, 162, 235, 0.3)', 'rgba(255, 110, 0, 0.3)'],
            'data' => [$p_paid, $p_unpaid,$p_part]
            ]
        ])
        ->options([]);
   
    $chartjs2 = app()->chartjs
        ->name('pieChartTest')
        ->type('pie')
        ->size(['width' => 400, 'height' => 200])
         ->labels(['الفواتير الغير مدفوعة', 'الفواتير المدفوعة', 'الفواتير المدفوعة جزئيا'])
        ->datasets([
            [
                'backgroundColor' => ['#FF6384', '#36A2EB', 'rgba(255, 110, 0, 0.8)'],
                'hoverBackgroundColor' => ['#FF6384', '#36A2EB'],
                'data' => [$p_paid, $p_unpaid, $p_part]
            ]
        ])
        ->options([]);
   return view('home',[
        'allInvoice'=>$all,
        'paidInvoice'=>$paid,
        'unpaidInvoice' => $unpaid,
        'partpaid'=> $partpaid,
        'p_paid' => $p_paid,
        'p_unpaid'=>$p_unpaid,
        'p_part'=>$p_part,
        'chartjs'=>$chartjs,
        'chartjs2'=> $chartjs2,
    ]);
}
}
