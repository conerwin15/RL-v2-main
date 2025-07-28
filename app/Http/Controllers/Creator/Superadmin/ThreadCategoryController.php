<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Log;
use Exception;
use Auth;   
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ ThreadCategory };
use Yajra\DataTables\DataTables;


class ThreadCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request->ajax()) {
            $query = ThreadCategory::where('status', 1);

            $threadCategories = $query->orderBy('name')->get();

            return Datatables::of($threadCategories, $request)
            ->addIndexColumn()
            ->addColumn('action', function($threadCategories){
                $href = url('/superadmin/threads/categories/'. $threadCategories->id);
                $editHref = url('/superadmin/threads/categories/'. $threadCategories->id);
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');
                $actionBtn = "<button class='nobtn color editCategory' type='submit' data-toggle='modal'
                                data-backdrop='static' data-keyboard='false' class='btn btn-success'
                                data-id='$threadCategories->id' data-name='$threadCategories->name'
                                    data-target='#editCategory'
                                data-href='$editHref'>
                                <i class='fa fa-pencil' aria-hidden='true'></i>$edit
                            </button>";
                $actionBtn = $actionBtn . "<button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";            
                return $actionBtn;
            }) 
            ->rawColumns(['action'])
            ->make(true);
        } 
        return view('creator.superadmin.thread-categories.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            request()->validate([
                'name' => 'required|min:2'
            ]);

            $threadCategory       = new ThreadCategory;
            $threadCategory->name = $request->name;
            $threadCategory->created_by = Auth::user()->id;
            $threadCategory->save();
  
            return response()->json(['success' => true, "messsage" => "{{ __('lang.thread-category-added') }}" ], 200);
        }  catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> $e->errors()], 200);
        } 
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try
        {
            request()->validate([
                'id' => 'required|exists:thread_categories,id',
                'name'        => 'required|min:2'
            ]);

            $isUpdated = ThreadCategory::where('id', $request->id)
                ->update(['name' => $request->name, 'updated_by'=> Auth::user()->id]);

            if($isUpdated == 0)
            {
                return response()->json(['success' => true, "messsage" => "{{ __('lang.thread-category-not-updated') }}"], 500);
            }
            
            return response()->json(['success' => true, "messsage" => "{{ __('lang.thread-category-updated') }}"], 200);
        }  catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> $e->errors()], 200);
        }   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $update = [
            'status' => 0,
            'deleted_at' => date("Y-m-d h:i:s")
        ];

        ThreadCategory::where('id', $id)->update($update); 
        return redirect()->back()
                         ->with('success', \Lang::get('lang.thread-category').' '.\Lang::get('lang.deleted-successfully')); 
        
    }	
    
}
