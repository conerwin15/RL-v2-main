<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Log;
use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\Models\{ Group, User, NewsPromotion, SalesTip };


class GroupController extends Controller
{
    /**
     * Display a listing of the Active courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        if($request->ajax()) {
            $searchByName = trim($request->query('name'));
            $query = Group::orderBy('name', 'asc');
            if (!empty($searchByName)) {
                $query = $query->where('name', 'like', '%' . $searchByName . '%');
            }
            $groups = $query->get();

            return Datatables::of($groups, $request)
            ->addIndexColumn()
            ->addColumn('action', function($groups){
                $href =  url('/superadmin/groups/'. $groups->id);
                $edithref = url('/superadmin/groups/'. $groups->id);
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');
                $actionBtn = "  <button class='editGroup btn btn-primary' type='submit' data-backdrop='static' data-keyboard='false' data-id='$groups->id'
                data-group='$groups->name' data-description='$groups->description'
                data-href='$edithref'>
                <i class='fa fa-pencil' aria-hidden='true'></i>  $edit </button> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('creator.superadmin.groups.index');
    }

     /**
     * Store a newly created group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
        
            $validator = Validator::make($request->all(), [
                'name'        => 'required|min:2',
                'description' => 'required|min:6'
            ]);
         
            if (!$validator->passes()) {
                return response()->json(['success' => false, "message" => $validator->errors()], 200);
            } 

            $group              = new Group;
            $group->name        = $request->name;
            $group->description = $request->description;
            $group->save();
  
        
            return response()->json(['success' => true, "messsage" => "{{ __('lang.group-added') }}"], 200);
        }  catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        } 
    }

    /*
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:groups,id',
                'name'        => 'required|min:2',
                'description' => 'required|min:6'
            ]);
         
            if (!$validator->passes()) {
                return response()->json(['success' => false, "message" => $validator->errors()], 200);
            } 
        
            $isUpdated = Group::where('id', $request->id)
                ->update(['name' => $request->name, 'description'=>$request->description]);

            if($isUpdated == 0)
            {
                return response()->json(['success' => true, "messsage" => "{{ __('lang.group-not-updated') }}"], 500);
            }
            
            return response()->json(['success' => true, "messsage" => "{{ __('lang.group-updated') }}"], 200);
        }  catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }      
    }       
    
    /**
     * Soft delete the specified group from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {  
        // update user
        User::where('group_id', $id)->update(['group_id' => null]);
        
        // update news & promotion
        NewsPromotion::where('group_id', $id)->update(['group_id' => null]);
        
        // update sales & tips
        SalesTip::where('group_id', $id)->update(['group_id' => null]);
       
        Group::where('id', $id)->delete();
        return redirect()->back()
                         ->with('success', \Lang::get('lang.group').' '.\Lang::get('lang.deleted-successfully')); 
        
    }	
}
