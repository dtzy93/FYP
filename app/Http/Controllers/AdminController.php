<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class AdminController extends Controller
{
    public function list(){
        $data['getRecord'] = User::getAdmin();
        $data['header_title'] = "Admin List";
        return view('admin.admin.list',$data);
    }

    public function add(){
        
        $data['header_title'] = "Add new Admin";
        return view('admin.admin.add',$data);
    }

    public function insert($id, Request $request){

        request()->validate([
            'email' => 'required|email|unique:users'
        ]);

        $user = new User;
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = Hash::make($request->password);
        $user->user_type = 1;
        $user->save();

        return redirect('admin/admin/list')->with('success', "Admin successfully created.");
    }

    public function edit($id){
        
        $data['getRecord'] = User::getSingle($id);
        if(!empty($data['getRecord'])){
            $data['header_title'] = "Edit Admin";
            return view('admin.admin.edit',$data);
        }
        else{
            abort(404);
        }
        
    }

    public function update($id, Request $request){

        request()->validate([
            'email' => 'required|email|unique:users,email,'.$id
        ]);


        $user = User::getSingle($id);
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = Hash::make($request->password);
        if(!empty($request->password)){
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect('admin/admin/list')->with('success', "Admin successfully updated.");
    }

    public function deleteUser(Request $request,$id){
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);
        $user = User::getSingle($id);
        $user->is_delete = 1;
        $user->reason = $request->reason;
        $user->save();

        return redirect('admin/admin/list')->with('success', "Admin successfully deleted");
    }

    public function showDeletedAdmins()
    {
        $data['header_title'] = "Deleted Admin List";
        // Fetching users marked as deleted and who are admins
        $deletedAdmins = User::where('user_type', 1)
                             ->where('is_delete', 1)
                             ->get();

        return view('admin.admin.deleted', compact('deletedAdmins'),$data);
    }

    public function recoverAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->is_delete = 0;  // Mark as not deleted
        $user->reason = null;  // Clear the reason
        $user->save();

        return redirect()->route('admin.showDeletedAdmins')->with('success', 'User recovered successfully.');
    }
}
