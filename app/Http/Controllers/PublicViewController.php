<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Cart;
use App\Models\LearningPackage;
use App\Models\LearningPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Log;

class PublicViewController extends Controller
{

    public function index(Request $request)
    {
        $limit = 4;
        $banners = Banner::where('status', 1)->take($limit)->get();
        $query = $this->query();
        $allPackages = $query->where('publish', 1)->paginate($limit);
        
        $packages = $this->managePackages($allPackages);
        
        //fetch cart items
        $cartPackages = $this->cartPackages();

        return view('learners.banner.banner', compact('banners', 'packages', 'cartPackages'))->with('index', (request()->input('page', 1) - 1) * $limit);
    }


    public function packageDetails(Request $request, $id)
    {
        $packageDetail = LearningPackage::with('category', 'learningPath', 'learningPath.resources')->where('id', $id)->first();
        $reallybotCount = $request->reallybotCount;
        $courseCount = $request->courseCount;
        $mediaCount = $request->mediaCount;
        $cartPackages = $this->cartPackages();
        return view('public.package-details', compact('packageDetail' ,'reallybotCount' ,'courseCount' ,'mediaCount' ,'cartPackages'));  
    }
    public function searchPackage(Request $request)
    { 
        $searchPackage = LearningPackage::with([
            'category' => function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->select(['id as categ_id', 'name as cat_name']);
            },
            'subCategory' => function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->select(['id as sub_categ_id', 'name as sub_cat_name']);
            }
        ])
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('category', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('subCategory', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    });
            })
            ->where('publish', 1)
            ->select(['id', 'name', 'category_id', 'sub_category_id'])
            ->get();
            
        $limit = 4;
        $banners = Banner::where('status', 1)->take($limit)->get();
        $query = $this->query();
        $allPackages = $query->whereIn('id',  $searchPackage->pluck('id')->all())->paginate($limit);

        $packages = $this->managePackages($allPackages);
        $cartPackages = $this->cartPackages();
        return view('learners.banner.banner', compact('banners', 'packages', 'cartPackages'))->with('searchPackage', (request()->input('page', 1) - 1) * $limit);
    }
    
    public function cartPackages()
    {
        $userId = Auth::id();
        if ($userId) {
            $cartPackageId = Cart::where('user_id', $userId)->get('package_id');
            $cartPackages = LearningPackage::whereIn('id', $cartPackageId)->get();


        } else {
            $cartPackageIds = Session::get('cart', []);
            $cartPackages = LearningPackage::whereIn('id', $cartPackageIds)->get();
        }
        return $cartPackages;
    }

    private function query()
    {
        $query =  LearningPackage::with('category')
        ->withCount([ 'learningPath' => function($query){
            $query->where('iframe_link', 'like', '%reallybot%');
        }])->with(['learningPath' => function($query){
            $query->select('learning_paths.id');
        }])->with(['learningPath.resources' => function($query){
            $query->select(['learning_path_resources.id', 'learning_path_resources.learning_path_id', 'learning_path_resources.link', 'learning_path_resources.type']);
        }]);
        return $query;
    }
    private function managePackages($packages)
    {
        foreach($packages as $package){
            $package->courseCount = 0;
            $package->mediaCount = 0;
            if(count($package->learningPath) !=0){
                foreach($package->learningPath as $learningPath){
                    if(count($learningPath->resources) !=0){
                        foreach($learningPath->resources as $resource){
                            if(strpos($resource->link, 'reallybot'))
                                $package->learning_path_count++;
                            if($resource->type == 'course_link')
                                $package->courseCount++;
                            if($resource->type == 'media_link')
                            $package->mediaCount++;
                        }
                    }
                }
            }
        }
        return $packages;
    }
}
