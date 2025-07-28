<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Log;
use Auth;
use File;
use Config;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Banner};
use Yajra\DataTables\DataTables;

class BannerResource extends Controller
{
    public $storagePath;

    public function __construct()
    {
       $this->storagePath = storage_path('app/public' . Config::get('constant.BANNER_STORAGE'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->query('name'));
        $query = Banner::where('status', 1)->orderBy('id', 'desc');
        if (!empty($search)) {
            $query->where('heading', 'like', '%' . $search . '%');
        }

        $banners = $query->get();
        $totalBanner = count($banners);

        if ($request->ajax())
        {
            return Datatables::of($banners)
            ->addIndexColumn()
            ->editColumn('description', function($banners)
            {
               return $banners->description;
            })
            ->editColumn('created_by', function($banners)
            {
               return ucfirst($banners->createdBy->name);
            })
            ->editColumn('image', function($banners)
            {
                $image = asset('storage' . Config::get('constant.BANNER_STORAGE')).'/'.$banners->image;
                return $image;
            })
            ->escapeColumns([])
            ->addColumn('action', function($banners){
                $href = url('/superadmin/banners/'. $banners->id);
                $edithref = url('/superadmin/banners/'. $banners->id);
                $view = \Lang::get('lang.view');
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');

                $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> $view </a><a href='$href/edit'><i class='fa fa-pencil' aria-hidden='true'></i> $edit </a><button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
               return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('creator.superadmin.banners.index', compact('totalBanner'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('creator.superadmin.banners.create');
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
            'heading' => 'required|min:3',
            'description' => 'required|min:2',
            'image' => 'required'
        ]);

        $banner = new Banner;
        $banner->heading = $request->heading;
        $banner->description = $request->description;
        $banner->created_by = Auth::user()->id;
        // file upload
        if($request->hasFile('image')) {
            request()->validate([
                'image' => 'mimes:png|required|max:1048576'  // 1048 mb
            ]);

            $file = $request->file('image');

            $uploadFilename =  md5(microtime()) .'.'.$file->getClientOriginalExtension();

            // check directory exist or not
            $path = $this->storagePath;

            if(!is_dir($path)) {
                File::makeDirectory($path, $mode = 0775, true, true);
            }
            $file->move($this->storagePath, $uploadFilename);
            $banner->image = $uploadFilename;

        }
        $banner->save();

        return redirect("superadmin/banners")->with('success',\Lang::get('lang.package').' '.\Lang::get('lang.created-successfully')); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $banner = Banner::findOrfail($id);
        $path = asset('storage' . Config::get('constant.BANNER_STORAGE'));
        return view('creator.superadmin.banners.show', compact( 'banner', 'path'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $banner = Banner::findOrfail($id);
       return view('creator.superadmin.banners.edit', compact('banner'));
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
            'banner_id' => 'required|exists:banners,id',
            'heading' => 'required|min:3',
            'description' => 'required|min:2'
        ]);

        $banner = Banner::findOrFail($request->banner_id);
        $update = [ 
            'heading' => $request->heading,
            'description'  => $request->description
        ];

        if($request->hasFile('image'))
        {
            request()->validate([
                'image' => 'mimes:png|required|max:1048576' // 1048 mb
            ]);

            //delete file from storage
                if($banner->image != null) {
                    $imagePath = $this->storagePath . $banner->image;
                    if(file_exists($imagePath)) {
                        unlink($imagePath);
                  }
            }

            $file = $request->file('image');

            $uploadFilename =  md5(microtime()) .'.'.$file->getClientOriginalExtension();

            // check directory exist or not
            $path = $this->storagePath;

            $file->move($this->storagePath, $uploadFilename);

            $update['image'] = $uploadFilename;
        }

        $isUpdated = Banner::where('id', $request->banner_id)->update($update);

        if($isUpdated == 0)
        {
            return redirect()->back()->with('error', \Lang::get('lang.unable-to-update'));
        }

        return redirect('/superadmin/banners')->with('success',\Lang::get('lang.banner').' '.\Lang::get('lang.updated-successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        $isDeleted = Banner::where('id', $id)->update(['status' => false]);

        if($isDeleted == 0)
        {
            return back()->with('error', \Lang::get('lang. unable-to-delete'));
        }

        return back()->with('success', \Lang::get('lang.banner-delete'));
    }
}
