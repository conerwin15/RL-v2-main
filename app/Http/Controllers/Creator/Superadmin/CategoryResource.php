<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Log;
use Exception;
use Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Category};
use Yajra\DataTables\DataTables;

class CategoryResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $search = trim($request->query('name'));
            $query = Category::whereNull('parent')->orderBy('id', 'desc');
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            $categories = $query->get();
            return Datatables::of($categories)
            ->addIndexColumn()
            ->editColumn('created_by', function($categories)
            {
               return ucfirst($categories->createdBy->name);
            })
            ->addColumn('action', function($categories){
                $href = url('/superadmin/categories/'. $categories->id);
                $edithref = url('/superadmin/categories/'. $categories->id);
                $view = \Lang::get('lang.sub-categories');
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');

                $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> $view </a> <button class='editCategory' type='submit' data-backdrop='static' data-keyboard='false'
                data-id='$categories->id' data-category='$categories->name' data-href='$edithref'>
                <i class='fa fa-pencil' aria-hidden='true'></i>  $edit </button> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
               return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('creator.superadmin.categories.index');
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

            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2'
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, "messsage" => $validator->errors()], 200);
            }

                $category = new Category();
                $category->name = $request->name;
                $category->created_by = Auth::user()->id;
                if(isset($request->category) && ($request->category != null))
                {
                    $category->parent = $request->category;
                }
                $category->save();
                return response()->json(['success' => true, "messsage" => "{{ __('lang.category-added') }}"], 200);

        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $subCategories = Category::where('parent', $id)->get();

        if ($request->ajax())
        {
            return Datatables::of($subCategories)
            ->addIndexColumn()
            ->editColumn('created_by', function($subCategories)
            {
               return ucfirst($subCategories->createdBy->name);
            })

            ->addColumn('action', function($subCategories){
                $href = url('/superadmin/categories/'. $subCategories->id);
                $edithref = url('/superadmin/categories/'. $subCategories->id);
                $view = \Lang::get('lang.view');
                $delete = \Lang::get('lang.delete');

                $actionBtn = "<button class='editSubCat' type='submit' data-backdrop='static' data-keyboard='false' data-id='$subCategories->id' data-name='$subCategories->name' data-href='$edithref'>
                <i class='fa fa-pencil' aria-hidden='true'></i>  Edit </button> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
               return $actionBtn;
            })
            ->editColumn('created_at', function($subCategories)
            {
               return (date('d M Y', strtotime($subCategories->created_at)));
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('creator.superadmin.categories.show', compact('id'));
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
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:categories,id',
                'name' => 'required|min:2',
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, "messsage" => $validator->errors()], 200);
            }

            $isUpdated = Category::where('id', $request->id)
                                ->update(['name' => $request->name]);
            if($isUpdated == 0)
            {
                return response()->json(['success' => true, "messsage" => "{{ __('lang.category-not-updated') }}"], 500);
            }

            return response()->json(['success' => true, "messsage" => "{{ __('lang.category-updated') }}"], 200);
        }  catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
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
        $categoryData = Category::findOrfail($id);

        $isdeleted = $categoryData->delete();

        if($isdeleted == 0) {
            throw new \Exception(\Lang::get('lang.unable-to-delete'));
        }

        return response()->json(['success' => true, "messsage" =>  \Lang::get('lang.deleted-successfully')], 200);

    }
}
