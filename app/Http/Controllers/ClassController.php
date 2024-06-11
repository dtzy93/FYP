<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\ClassModel;

class ClassController extends Controller
{
    public function list(){
        $data['getRecord'] = ClassModel::getRecord();
        
        $data['header_title'] = "CLass List";
        return view('admin.class.list', $data);
    }

    public function add(){
        $data['header_title'] = "Add New Class";
        return view('admin.class.add', $data);
    }

    public function insert(Request $request){
        $save = new ClassModel;
        $save->name = $request->name;
        $save->status = $request->status;
        $save->created_by = Auth::user()->id;
        $save->save();
        
        return redirect('admin/class/list')->with('success', "Class Successfully Created");
    }

    public function edit($id){
        $data['getRecord'] = ClassModel::getSingle($id);
        if(!empty($data['getRecord'])){
            $data['header_title'] = "Edit Class";
            return view('admin.class.edit', $data);
        }
        else{
            abort(404);
        }
        
    }

    public function update($id, Request $request){
        $save = ClassModel::getSingle($id);
        $save->name = $request->name;
        $save->status = $request->status;
        $save->save();

        return redirect('admin/class/list')->with('success', "Class Successfully Updated");
    }

    public function delete(Request $request, $id)
{
    $deletedBy = auth()->id();
    
    $request->validate([
        'reason' => 'required|string|max:255',
    ]);

    $class = ClassModel::getSingle($id);

    if (!$class) {
        // Handle the case where the class with the given ID does not exist
        abort(404);
    }

    $class->is_delete = 1;
    $class->reason = $request->reason; // Set the reason directly on the class object
    $class->deleted_by = $deletedBy;
    $class->save();

    return redirect()->back()->with('success', "Class Successfully Deleted");
}

    public function viewArchived()
    {
        $getRecord = ClassModel::getArchivedRecords();
        $header_title = 'Archived Classes';
        return view('admin.class.archived', compact('getRecord', 'header_title'));
    }

    public function restore($id) {
        $class = ClassModel::getSingle($id);
        $class->is_delete = 0;
        $class->reason = NULL;
        $class->save();
    
        return redirect()->back()->with('success', "Class Successfully Restored");
    }
    
}
