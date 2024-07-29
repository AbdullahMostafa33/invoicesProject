<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users=User::all();      

        return view('users.show_users',['users'=> $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();

        return view('users.Add_user', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
     
        $request->validate( [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status'=> 'required',
            'roles_name' => 'required',
        ]);
        $request['password']= Hash::make($request['password']);
      
      $user=  User::create($request->all());
        $role = Role::where('name', $request->roles_name)->first();
        $user->roles()->attach($role);

        session()->flash('Add', 'تمت الاضافة بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();

        return view('users.edit',['user'=>$user, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => 'required',
            'roles_name' => 'required',
        ]);
        $request['password'] = Hash::make($request['password']);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->status = $request->input('status');
        $user->roles = $request->input('roles_name');       
        $user->password = $request->input('password');      
        $user->save();

        $user->roles()->detach();
        $role = Role::where('name', $request->roles)->first();
        $user->roles()->attach($role);

        session()->flash('edit', 'تم التعديل بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user=User::find($request->user_id); 
        $user->delete();
        session()->flash('delete', 'تم الحذف بنجاح');
        return back();

    }
}
