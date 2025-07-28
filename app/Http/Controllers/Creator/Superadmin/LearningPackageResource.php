<?php

namespace App\Http\Controllers\Creator\Superadmin;

use App\Models\PackagePriceHistory;
use DB;
use Str;
use Log;
use Auth;
use File;
use Config;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{LearningPackage, Category, PackageLearningPath, LearningPath};
use Yajra\DataTables\DataTables;

class LearningPackageResource extends Controller
{

    public $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/public' . Config::get('constant.LEARNING_PACKAGE'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::whereNull('parent')->orderBy('id', 'desc')->get();
        $subCategories = count($categories) ? Category::where('parent', $categories[0]->id)->orderBy('id', 'desc')->get() : [];
        $filter_category = empty($request->query('filter_category')) ? 0 : $request->query('filter_category');
        $filter_sub_category = empty($request->query('filter_sub_category')) ? 0 : $request->query('filter_sub_category');

        $query = LearningPackage::with('category', 'subCategory')->withCount('learningPaths');

        if ($request->query('filter_category') && ($request->query('filter_category')) != 0) {
            $query = $query->where('category_id', $filter_category)->orderBy('id', 'desc');
        }

        if ($request->query('filter_sub_category') && ($request->query('filter_sub_category')) != 0) {
            $query = $query->where('sub_category_id', $filter_sub_category)->orderBy('id', 'desc');
        }

        if ($request->ajax()) {
            $search = trim($request->query('name'));
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            $packages = $query->get();
            return Datatables::of($packages)
                ->addIndexColumn()
                ->editColumn('description', function ($packages) {
                    return $packages->description;
                })
                ->editColumn('category', function ($packages) {
                    return $packages->category->name;
                })
                ->editColumn('sub_category', function ($packages) {
                    return $packages->subCategory->name;
                })
                ->editColumn('no_of_learning_paths', function ($packages) {
                    return $packages->learningPaths->count();
                })
                ->editColumn('created_on', function ($packages) {
                    return $packages->created_at->format('d-m-Y');
                })
                ->editColumn('published_unpublished_on', function ($packages) {
                    return $packages->updated_at->format('d-m-Y');
                })
                ->editColumn('image', function ($packages) {
                    $image = asset('storage' . Config::get('constant.LEARNING_PACKAGE')) . '/' . $packages->image;
                    return $image;
                })
                ->escapeColumns([])
                ->addColumn('action', function ($packages) {
                    $href = url('/superadmin/packages/' . $packages->id);
                    $edithref = url('/superadmin/packages/' . $packages->id . '/edit');
                    $managePath = url('/superadmin/package/manage/' . $packages->id);
                    $edit = \Lang::get('lang.edit');
                    $delete = \Lang::get('lang.delete');
                    $view = \Lang::get('lang.view');
                    $viewUsers = \Lang::get('lang.view-users');
                    $managePackage = \Lang::get('lang.manage');
                    if($packages->publish){
                        $publishPath = url("/superadmin/un-publish/package/$packages->id");
                    } else {
                        $publishPath = url("/superadmin/publish/package/$packages->id");
                    }
                    $packagePublish = $packages->publish == 1 ? \Lang::get('lang.un-publish') : \Lang::get('lang.publish');
                    $actionBtn = "<div class='action-btn'> <a href='$managePath' style='min-width:fit-content;'><i class='fa fa-users' aria-hidden='true'></i> $managePackage </a>
                <button type='button' class='text-success update-package-status' data-href='$publishPath' data-role='superadmin'>$packagePublish </button>
                <a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> $view </a><a href=''><i class='fa fa-eye' aria-hidden='true'></i> $viewUsers </a><a href='$href/edit'><i class='fa fa-pencil' aria-hidden='true'></i> $edit </a><button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button></div>";
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('creator.superadmin.packages.index', compact('categories', 'subCategories'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::whereNull('parent')->orderBy('id', 'desc')->get();
        $subCategories = count($categories) ? Category::where('parent', $categories[0]->id)->orderBy('id', 'desc')->get() : [];

        return view('creator.superadmin.packages.create', compact(['categories', 'subCategories']));
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
            'category' => 'required|exists:categories,id',
            'sub_category' => 'required|exists:categories,id',
            'name' => 'required|min:3',
            'description' => 'required|min:2',
            'image' => 'required',
            'price' => 'required',
            'discounted_price' => 'required|numeric|max:' . $request->input('price'),
        ]);

        $package = new LearningPackage;
        $package->unique_ID = "LP".rand ( 10000 , 99999 );
        $package->name = $request->name;
        $package->description = $request->description;
        $package->price = $request->price;
        $package->category_id = $request->category;
        $package->discount_price = $request->discounted_price;
        $package->sub_category_id = $request->sub_category;

        // file upload
        $mediaFormats = implode(',', Config::get("constant.SUPPORTED_IMAGE_FORAMTS"));

        if ($request->hasFile('image')) {
            request()->validate([
                'image' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
            ]);

            $file = $request->file('image');

            $uploadFilename = md5(microtime()) . '.' . $file->getClientOriginalExtension();

            // check directory exist or not
            $path = $this->storagePath;

            if (!is_dir($path)) {
                File::makeDirectory($path, $mode = 0775, true, true);
            }
            $file->move($this->storagePath, $uploadFilename);
            $package->image = $uploadFilename;

        }
        $package->save();

        return redirect("superadmin/packages")->with('success', \Lang::get('lang.package') . ' ' . \Lang::get('lang.created-successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $package = LearningPackage::with('priceHistories')->findOrFail($id);
        $path = asset('storage' . Config::get('constant.LEARNING_PACKAGE'));
        return view('creator.superadmin.packages.show', compact('package', 'path'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $package = LearningPackage::findOrfail($id);
        $categories = Category::whereNull('parent')->orderBy('id', 'desc')->get();
        $subCategories = Category::where('parent', $package->category_id)->orderBy('id', 'desc')->get();
        return view('creator.superadmin.packages.edit', compact(['categories', 'subCategories', 'package']));
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
        $this->validate($request, [
            'category' => 'required|exists:categories,id',
            'sub_category' => 'required|exists:categories,id',
            'package_id' => 'required|exists:learning_packages,id',
            'name' => 'required|min:3',
            'description' => 'required|min:2',
            'price' => 'required',
            'discounted_price' => 'required'
        ]);

        $package = LearningPackage::findOrFail($request->package_id);

        $existingPrice = $package->price;
        $existingDiscountPrice = $package->discount_price;

        $user = Auth::user();
        if ($existingPrice != $request->price)
        {
            $updatedPrice = new PackagePriceHistory();
            $updatedPrice->user_id = $user->id;
            $updatedPrice->package_id = $id;
            $updatedPrice->price_type = 'price';
            $updatedPrice->updated_price = $existingPrice;
            $updatedPrice->save();
        }
        if ($existingDiscountPrice != $request->discounted_price)
        {
            $updatedPrice = new PackagePriceHistory();
            $updatedPrice->user_id = $user->id;
            $updatedPrice->package_id = $id;
            $updatedPrice->price_type = 'discounted_price';
            $updatedPrice->updated_price = $existingDiscountPrice;
            $updatedPrice->save();
        }

        $update = [
            'name' => $request->name,
            'category_id' => $request->category,
            'sub_category_id' => $request->sub_category,
            'description'  => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discounted_price,
        ];

        if($request->hasFile('image'))
        {
            $mediaFormats = implode(',', Config::get("constant.SUPPORTED_IMAGE_FORAMTS"));
            request()->validate([
                'image' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
            ]);

            //delete file from storage
            if($package->image != null) {
                $imagePath = $this->storagePath . $package->image;
                if(file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $file = $request->file('image');

            $uploadFilename = md5(microtime()) . '.' . $file->getClientOriginalExtension();

            // check directory exist or not
            $path = $this->storagePath;

            $file->move($this->storagePath, $uploadFilename);

            $update['image'] = $uploadFilename;
        }

        $isUpdated = LearningPackage::where('id', $request->package_id)->update($update);

        if ($isUpdated == 0) {
            return redirect()->back()->with('error', \Lang::get('lang.unable-to-update'));
        }

        return redirect('/superadmin/packages')->with('success', \Lang::get('lang.package') . ' ' . \Lang::get('lang.updated-successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $package = LearningPackage::find($id);
        //delete file from storage
        if ($package->image != null) {
            $mediaPath = $this->storagePath . $package->image;
            if (file_exists($mediaPath)) {
                unlink($this->storagePath . $package->image);
            }
        }
        $isDeleted = $package->delete();

        if ($isDeleted == 0) {
            return back()->with('error', \Lang::get('lang. unable-to-delete'));
        }

        return back()->with('success', \Lang::get('lang.news-delete'));
    }

    public function getSubCategory($categoryId)
    {
        try {

            $subCategories = Category::where('parent', $categoryId)->orderBy('id', 'desc')->get();
            return $subCategories;
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    public function managePackage(Request $request, $id)
    {
        Log::debug("search learning path");
        $query = PackageLearningPath::with('learningPaths')->whereHas('learningPaths')->where('learning_package_id', $id);
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $search = trim($request->query('name'));
        if (!empty($search)) {
            $query = $query->whereHas('learningPaths', function ($s) use ($search) {
                $s->where('name', 'like', '%' . $search . '%');
            });
        }

        $packageLearningPaths = $query->orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return Datatables::of($packageLearningPaths, $request)
                ->addIndexColumn()
                ->editColumn('unique_ID', function ($packageLearningPaths) {
                    return $packageLearningPaths->learningPaths->unique_ID;
                })
                ->editColumn('name', function ($packageLearningPaths) {
                    return $packageLearningPaths->learningPaths->name;
                })
                ->editColumn('image', function ($packageLearningPaths) {
                    $image = asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE')) . '/' . $packageLearningPaths->learningPaths->featured_image;
                    return $image;
                })
                ->editColumn('description', function ($packageLearningPaths) {
                    return $packageLearningPaths->learningPaths->description;
                })
                ->escapeColumns([])
                ->addColumn('action', function ($packageLearningPaths) {
                    $href = url('/superadmin/package/' . $packageLearningPaths->id . '/remove/learning-paths');
                    $delete = \Lang::get('lang.delete');
                    $actionBtn = "<button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('creator.superadmin.packages.manage_pacakge', compact('id'));
    }

    // show assigend list
    public function assignLearningPath(Request $request, $packageId)
    {
        DB::enableQueryLog();
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $search = trim($request->query('search'));

        $assigedLearningPaths = PackageLearningPath::where('learning_package_id', $packageId)->pluck('learning_path_id');
        $query = LearningPath::whereNotIn('id', $assigedLearningPaths)
            ->orderby('created_at', 'asc');

        if (!empty($search)) {
            $query = $query->where('name', 'like', '%' . $search . '%');
        }

        $learningPaths = $query->paginate($limit);
        return view('creator.superadmin.packages.assign_learning_paths', compact('learningPaths', 'packageId'))->with('index', ($page - 1) * $limit);
    }

    // assign learning paths
    public function addLearningPath(Request $request)
    {
        $package = LearningPackage::findorFail($request->package_id);
        if ((!isset($request->assign_learning_paths)) && $request->assignAll == null) {
            return redirect()->back()->with('error', \Lang::get('lang.please-select-learning_paths'));
        }

        if ($request->assignAll == -1) {

            $assignedlearningPaths = PackageLearningPath::where('learning_package_id', $request->package_id)->pluck('learning_path_id');
            $learningPaths = LearningPath::whereNotIn('id', $assignedlearningPaths)
                ->orderby('created_at', 'asc')->select('id')->get();

            foreach ($learningPaths as $key => $learningPath) {
                $packageLearningPath = new PackageLearningPath;
                $packageLearningPath->learning_package_id = $request->package_id;
                $packageLearningPath->learning_path_id = $learningPath->id;
                $packageLearningPath->save();

                // update bot_code
                $lp = LearningPath::findorfail($learningPath->id);
                $lp->bot_code = $lp->type . '_' . $package->category_id . '_' . $package->sub_category_id . '_' . $lp->level . '_' . date("Y") . '_' . Str::random(9);
                $lp->save();
            }
        } else {
            foreach ($request->assign_learning_paths as $key => $value) {
                $packageLearningPath = new PackageLearningPath;
                $packageLearningPath->learning_package_id = $request->package_id;
                $packageLearningPath->learning_path_id = $value;
                $packageLearningPath->save();

                // update bot_code
                $lp = LearningPath::findorfail($value);
                $lp->bot_code = $lp->type . '_' . $package->category_id . '_' . $package->sub_category_id . '_' . $lp->level . '_' . date("Y") . '_' . Str::random(9);
                $lp->save();
            }
        }

        return redirect('superadmin/package/manage/' . $request->package_id)
            ->with('success', \Lang::get('lang.learningpath-assign'));
    }

    public function removeLearningPaths(Request $request, $id)
    {
        $packageLearningPath = PackageLearningPath::findOrfail($id);
        $isDeleted = $packageLearningPath->delete();
        if ($isDeleted == 0) {
            throw new \Exception(\Lang::get('lang.unable-to-delete'));
        }

        return back()->with('success', \Lang::get('lang.learning-path-delete'));
    }

    public function publishPackage($id)
    {
        try {
            Log::debug('publish');
            $learningPackage = LearningPackage::findOrFail($id);
            $learningPackage->publish = true;
            $learningPackage->save();
            return response()->json(['success' => true, 'messsage' => 'Updated successfully']);
        } catch (\Exception $e) {
            Log::error($e);
            return  redirect()->back()->with(['success' => false, 'messsage' => 'Failed to publish/unpublish Learning Package'], 500);
        }
    }

    public function unPublishPackage($id)
    {
        try {
            Log::debug('unpublish');
            $learningPackage = LearningPackage::findOrFail($id);
            $learningPackage->publish = false;
            $learningPackage->save();
            return response()->json(['success' => true, 'messsage' => 'Updated successfully']);
        } catch (\Exception $e) {
            Log::error($e);
            return  redirect()->back()->with(['success' => false, 'messsage' => 'Failed to publish/unpublish Learning Package'], 500);
        }
    }
}