<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{


    // function __construct()
    // {

    //     $this->middleware('permission:عرض صلاحية', ['only' => ['index']]);
    //     $this->middleware('permission:اضافة صلاحية', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:تعديل صلاحية', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:حذف صلاحية', ['only' => ['destroy']]);
    // }

    public function index(Request $request)
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(5);
        return view('roles.index',['roles'=> $roles])
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
   
    public function create()
    {
        $permission = Permission::get();
        return view('roles.create', compact('permission'));
    }
   
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
        session()->flash('Add', 'تمت الاضافة بنجاح');
        return redirect()->route('roles.index');
           
    }
   
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();
        return view('roles.show', compact('role', 'rolePermissions'));
    }
    
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
       $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }
   
    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        session()->flash('edit', 'تم التعديل بنجاح');
        return redirect()->route('roles.index');
            
    }

    public function destroy(Role $role)
    {
        $role->delete();
        session()->flash('delete', 'تم الحذف بنجاح');
        return back();
    }
}
