<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections=Section::all();        
        return view('sections.sections',['sections'=>$sections]);
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
       $validate=$request->validate([
            'name'=> ['required','unique:sections'],
            'description'=> 'required',
       ],
       [
        'name.required'=>' اسم القسم مطلوب' ,
        'name.unique' => 'القسم موجود مسبقا',
        'description.required' => ' الوصف مطلوب'  
 
       ]);
        Section::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'created_by'=>Auth::user()->name,
        ]);
        session()->flash('Add', 'تمت الاضافة بنجاح');

        return redirect('/sections');
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id=$request->id;
        $validate = $request->validate(
            [
                'name' => ['required',
                            Rule::unique('sections')->ignore($id),
                               ],
                'description' => 'required',
            ],
            [
                'name.required' => ' اسم القسم مطلوب',
                'name.unique' => 'القسم موجود مسبقا',
                'description.required' => ' الوصف مطلوب'

            ]
        );
        $section=Section::find($request->id);
        $section->name= $request->name;
        $section->description = $request->description;
        $section->save();
        session()->flash('edit', 'تم التعديل بنجاح');
        return redirect('/sections');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Request $request)
    {
        $section = Section::find($request->id);
       
        $section->delete();
        session()->flash('delete','تم الحذف بنجاح');
        return redirect('/sections');
    }
}
