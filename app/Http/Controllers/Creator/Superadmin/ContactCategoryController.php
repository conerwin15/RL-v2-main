<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ ContactCategory, Role, Setting };
use Yajra\DataTables\DataTables;
use config;

class ContactCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {           
        $roles  = Role::whereNotIn('name', ['superadmin'])->get();      
        if($request->ajax()) {
            $searchByName = trim($request->query('name'));
            $query = ContactCategory::orderBy('category_name', 'asc');
            if (!empty($searchByName)) {

                $query = $query->where('category_name', 'like', '%' . $searchByName . '%');
            }
            $contactCategories = $query->with('roles')->get();

            return Datatables::of($contactCategories, $request)
            ->addIndexColumn()
            ->editColumn('role', function($contactCategories)
            {
                return toRoleLabel($contactCategories->roles)  ? ucfirst(toRoleLabel($contactCategories->roles->name)) : \Lang::get('lang.all');
            })
            ->editColumn('created_on', function($contactCategories)
            {
                return  date('d M Y', strtotime($contactCategories->created_at));
            })
            ->addColumn('action', function($contactCategories){
                $href = 'contact-categories/'. $contactCategories->id;
                $editHref = $href . '/edit';
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');
                $actionBtn = "<a href='$editHref'><i class='fa fa-pencil' aria-hidden='true'></i> $edit </a> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view( 'creator.superadmin.contact-categories.index', compact(['roles']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|min:6',
            'role'  => 'required',
            'email' => 'required'
        ]);

        $contactCategory                = new ContactCategory;
        $contactCategory->category_name = $request->name;
        $contactCategory->role_id       = ($request->role == -1) ? null : $request->role;
        $contactCategory->email         = $request->email; 
        $contactCategory->save();

        return redirect("superadmin/contact-categories")->with('success',\Lang::get('lang.contact-category').' '.\Lang::get('lang.created-successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contactCategory = ContactCategory::where('id',$id)->first();
        $roles  = Role::whereNotIn('name', ['superadmin'])->get(); 
        return view('creator.superadmin.contact-categories.edit', compact('contactCategory','roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'id'    => 'required|exists:contact_categories,id', 
            'name'  => 'required|min:6',
            'role'  => 'required',
            'email' => 'required'
        ]);

        $update = [
                 'category_name' => $request->name,
                 'role_id' => ($request->role == -1) ? null : $request->role,
                 'email' => $request->email
        ];
       
        $isUpdated = ContactCategory::where('id', $request->id)
                                    ->update($update);
        if($isUpdated == 0)
        {
             return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') ); 
        }

        return redirect("superadmin/contact-categories")->with("success",  \Lang::get('lang.contact-category') .' '. \Lang::get('lang.updated-successfully')); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contactCategory = ContactCategory::findOrfail($id);
        $contactCategory->delete();
        return redirect()->back()->with("success",  \Lang::get('lang.contact-category') .' '. \Lang::get('lang.deleted-successfully')); 
    }
}
