<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $sections= Section::all();
       $products=Product::all();
        return view('products.products',[
            'sections'=>$sections,
            'products' => $products,
        ]);
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
        $validate=$request->validate(
            [
                'name'=>'required',
                'description' => 'required',
                'section_id' => 'required',
            ],
            [
                'name.required' => ' اسم المنتج مطلوب',
                //'name.unique' => 'القسم موجود مسبقا',
                'description.required' => ' الوصف مطلوب',
                'section_id' => " القسم مطلوب",

            ]
        );
        Product::create($validate);
        session()->flash('Add','تمت الاضافة بنجاح');
        return redirect('/products');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validate = $request->validate(
            [
                'name' => 'required',
                'description' => 'required',
                'section_id' => 'required',
            ],
            [
                'name.required' => ' اسم المنتج مطلوب',
                'description.required' => ' الوصف مطلوب',
                'section_id' => " القسم مطلوب",

            ]
        );
        $product=Product::find($request->id);
        $product->update($validate);
        session()->flash('edit', 'تم التعديل بنجاح');
        return redirect('/products');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $product=Product::find($request->id);
        $product->delete();
        session()->flash('delete', 'تم الحذف بنجاح');
        return redirect('/products');
    }
}
