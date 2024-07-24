<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Invoice_attachment;
use App\Models\Invoice_detail;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        $user = User::find(1); // or User::find($userId);
        if ($user) {
          
                $user->assignRole('owner');
                // Verify if the role is assigned
                dd($user->roles);
            
        } else {
            dd('User is null');
        }
        if ($request->status == 'paid') {
            $invoices = Invoice::Where('Value_Status', 1)->get();
            $title = 'الفواتير المدفوعة';
        } else if ($request->status == 'unpaid') {
            $invoices = Invoice::Where('Value_Status', 2)->get();
            $title = 'الفواتير غير مدفوعة';
        } else if ($request->status == 'partly') {
            $invoices = Invoice::Where('Value_Status', 3)->get();
            $title = 'الفواتيرالمدفوعة جزئيا';
        } else if ($request->status == 3) {
            $invoices = Invoice::onlyTrashed()->get();
            $title = 'الفواتير  المؤرشفة';
        } else {
            $invoices = Invoice::all();
            $title =  "قائمة الفواتير";
        }
        return view('invoices.invoices', [
            'invoices' => $invoices,
            'title' => $title,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::all();
        return view('invoices.add_invoice', [
            'sections' => $sections,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceRequest $request)
    {
        $request['user'] = Auth::user()->name;
        $invoice = Invoice::create($request->all());

        $detail = new Invoice_detail();
        $detail->invoice_id = $invoice->id;
        $detail->Status = 'غير مدفوعة';
        $detail->Value_Status = 2;
        $detail->note = $request->note;
        $detail->user = Auth::user()->name;
        $detail->save();

        if ($request->hasFile('pic')) {

            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();

            $attachments = new Invoice_attachment();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $request->invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice->id;
            $attachments->save();

            // move pic
            $request->pic->move(public_path('Attachments/' . $request->invoice_number), $file_name);
        }
        session()->flash('Add', 'تمت الاضافة بنجاح');
        return redirect('/invoices/create');
    }

    /**
     * Display the specified resource. 
     */
    public function show($id) // show edit
    {
        $invoice = Invoice::find($id);
        $sections = Section::all();
        $products = Product::where('Section_id', $invoice->section_id)->get(['id', 'name']);
        return view('invoices.edit_invoice', [
            'sections' => $sections,
            'invoice' => $invoice,
            'products' => $products,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoices = Invoice::find($id);
        $details = Invoice_detail::where('invoice_id', $id)->get();
        $attachments = Invoice_attachment::where('invoice_id', $id)->get();
        return view("invoices.invoices_detail", [
            'invoices' => $invoices,
            'attachments' => $attachments,
            'details' => $details,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InvoiceRequest $request, $id)
    {
        $invoice = Invoice::find($id);
        $invoice->update($request->all());
        session()->flash('edit', 'تم التعديل بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Invoice::withTrashed()
            ->where('id', $request->id)->forceDelete();
        session()->flash('delete', 'تم الحذف بنجاح');
        return back();
    }
    //
    public function toArchive(Request $request)
    {
        Invoice::find($request->id)->delete();
        session()->flash('delete', 'تم النقل الي الارشيف بنجاح');
        return back();
    }

    public function restore(Request $request)
    {
        Invoice::withTrashed()
            ->where('id', $request->id)->restore();
        session()->flash('edit', 'تم الاستعادة  بنجاح');
        return back();
    }

    public function getProducts($id)
    {
        $products = Product::where('section_id', $id)->pluck('name', "id");
        return json_encode($products);
    }

    public function print(Invoice $invoice)
    {
        return view('invoices.print', ['invoices' => $invoice]);
    }

    public function payment(Invoice $invoice,Request $request)
    {
        $request->validate([
            'collectionAmount' => ['required', 'min:0'],
        ],[
            'collectionAmount.required' =>'مبلغ التحصيل مطلوب',
        ]);

         $invoice->Amount_collection+=$request->collectionAmount;
         if($invoice->Amount_collection>= $invoice->Total)
         {
             $invoice->Status='مدفوعة';
             $invoice->Value_Status=1;
             $invoice->Payment_Date=now();
        }
        else {
            $invoice->Status = 'مدفوعة جزئيا';
            $invoice->Value_Status = 3; 
        }
         $invoice->save();

        $detail = new Invoice_detail();
        $detail->invoice_id = $invoice->id;
        $detail->Status = $invoice->Status;
        $detail->Value_Status =  $invoice->Value_Status;
        $detail->note = $request->note;
        $detail->Payment_Date = date('Y/m/d');
        $detail->Amount_collection=$request->collectionAmount;
        $detail->user = Auth::user()->name;
        $detail->save();
        session()->flash('edit', 'تم التعديل بنجاح');
        return back();
    }
}
