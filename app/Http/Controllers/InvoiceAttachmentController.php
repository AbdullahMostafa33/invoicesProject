<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Invoice_attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    
        if ($request->hasFile('file_name')) {

            $image = $request->file('file_name');
            $file_name = $image->getClientOriginalName();
            $attachments = new Invoice_attachment();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $request->invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $request->invoice_id;
            $attachments->save();

            // move file
            $request->file_name->move(public_path('Attachments/' . $request->invoice_number), $file_name);
            session()->flash('Add', 'تمت الاضافة بنجاح');
            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice_attachment $invoice_attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice_attachment $invoice_attachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice_attachment $invoice_attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Invoice_attachment::find($request->id_file)->delete();
        Storage::disk('attachment')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم الحذف بنجاح');
                return back();             
    }

  public function download($folder,$name){
    
        if (Storage::disk('attachment')->exists($folder.'/' . $name)) {
            $filePath = public_path('Attachments/'. $folder . '/' . $name);
            return response()->download($filePath);
        } 
  }

    public function open($folder, $name)
    {
        if (Storage::disk('attachment')->exists($folder . '/' . $name)) {
            $filePath = public_path('Attachments/' . $folder . '/' . $name);
            return response()->file($filePath);
        }
    }
}
