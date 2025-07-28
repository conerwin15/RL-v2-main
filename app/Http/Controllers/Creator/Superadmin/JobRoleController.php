<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Log;
use Exception;
use Validator;
use App\Models\{JobRole, User, NewsPromotion, SalesTip };
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class JobRoleController extends Controller
{
    /**
     * Display a listing of the Active Jobroles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $searchByName = trim($request->query('name'));
            $query = JobRole::orderBy('name', 'asc');
            if (!empty($searchByName)) {
                $query = $query->where('name', 'like', '%' . $searchByName . '%');         
            }
            $jobRoles = $query->get();

            return Datatables::of($jobRoles, $request)
            ->addIndexColumn()
        
            ->addColumn('action', function($jobRoles){
                $href =  url('/superadmin/job-roles/'. $jobRoles->id);
                $edithref = url('/superadmin/job-roles/'. $jobRoles->id);
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');
                $actionBtn = "<button class='editJobRole btn btn-primary' type='submit' data-backdrop='static' data-keyboard='false' data-id='$jobRoles->id'
                data-jobrole='$jobRoles->name' data-description='$jobRoles->description'
                data-href='$edithref'>
                <i class='fa fa-pencil' aria-hidden='true'></i>  $edit </button> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('creator.superadmin.job-roles.index');
    }

     /**
     * Store a newly created job role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'description' => 'required|min:6'
            ]);
         
            if (!$validator->passes()) {
                return response()->json(['success' => false, "message" => $validator->errors()], 200);
            } 

            $jobRole 				= new JobRole;
            $jobRole->name          = $request->name;
            $jobRole->description   = $request->description;
            $jobRole->save();

            return response()->json(['success' => true, "messsage" => "{{ __('lang.jobrole-added') }}"], 200);
        } catch (Exception $e) {
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
        try{
            
            $validator = Validator::make($request->all(), [
                'id'          => 'required|exists:job_roles,id',
                'name'        => 'required|min:2',
                'description' => 'required|min:6'
            ]);
         
            if (!$validator->passes()) {
                return response()->json(['success' => false, "message" => $validator->errors()], 200);
            } 
            $isUpdated = JobRole::where('id', $request->id)
                                ->update(['name' => $request->name,'description' => $request->description]);
        
            if($isUpdated == 0)
            {
                return response()->json(['success' => true, "messsage" => "{{ __('lang.jobrole-not-updated') }}"], 200);
            }
        
            return response()->json(['success' => true, "messsage" => "{{ __('lang.jobrole-updated') }}"], 200);
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
        User::where('job_role_id', $id)->update(['job_role_id' => null]);
        
        // update news & promotion
        NewsPromotion::where('job_role_id', $id)->update(['job_role_id' => null]);
        
        // update sales & tips
        SalesTip::where('job_role_id', $id)->update(['job_role_id' => null]);
        
        JobRole::where('id', $id)->delete();
        return redirect()->back()->with("success",  \Lang::get('lang.job-role') .' '. \Lang::get('lang.deleted-successfully')); 
    } 

}
