<?php

namespace App\Http\Controllers\Creator\Shared;

use Exception;
use Storage;
use stdClass;
use DB;
use Zip;
use File;
use Auth;
use Config;
use Log;
use DOMDocument;
use Aws\S3\S3Client;
use Illuminate\Support\Str;
use ZanySoft\Zip\ZipManager;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\ValidationException;
use App\Models\{ UserLearningProgress, LearningPath, Group, JobRole, LearningPathResource, ScormPackage, ScromPackageResourceItem, Certificate, Role, User, Country, Region, Category };
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LearningPathResponseExport;

class LearningPathController extends Controller
{

    /**
     * Display a listing of the Active learning-path.
     *
     * @return \Illuminate\Http\Response
    */


    public function index(Request $request)
    {
        $routeSlug = $this->getRouteSlug();
        $search = trim($request->query('name'));
        if($request->ajax())
        {
            $user = Auth::user();
            $role = $user->roles[0]->name;
            $regionId = $user->region_id;

            $query = LearningPath::orderBy('name', 'asc')->with('createdBy')->where('status', 1);
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
            if($user->roles[0]->name != 'superadmin'){

                $learningPaths = $query->whereHas('users', function ($q) {
                            $q->whereIn('region_id', explode(',', Auth::user()->region_id));
                })->orWhere('created_by', Auth::user()->id)->get();
            } else {
                $learningPaths = $query->get();
            }

            return Datatables::of($learningPaths, $request)
                    ->addIndexColumn()
                    ->editColumn('image', function($learningPaths)
                    {
                        $image = asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE')).'/'.$learningPaths->featured_image;
                        return $image;
                    })
                    ->editColumn('name', function($learningPaths)
                    {
                        return $learningPaths->name;
                    })
                    ->editColumn('learners', function($learningPaths)
                    {
                        $role = Auth::user()->getRoleNames()->first();
                        if($role != 'superadmin') {
                             return $learningPaths->usersCountByRegion();
                        } else {
                            return $learningPaths->users->count();
                        }
                    })

                    ->editColumn('created_by', function($learningPaths)
                    {
                        return  $learningPaths->createdBy ? ucfirst($learningPaths->createdBy->name) : '';
                    })

                    ->editColumn('uploaded_by', function($learningPaths)
                    {
                        return  $learningPaths->uploaded_by ? ucwords($learningPaths->uploaded_by) : ucfirst($learningPaths->createdBy->name);
                    })

                    ->editColumn('created_on', function($learningPaths)
                    {
                        return  date('d M Y', strtotime($learningPaths->created_at));
                    })
                   ->addColumn('action', function($learningPaths) {
    $user = Auth::user();
    $routeSlug = $user->getRoleNames()->first();

    $href = 'learning-paths/' . $learningPaths->id;
    $editHref = $href . '/edit';
    $learnerHref = 'learners/' . $learningPaths->id;

    $view = \Lang::get('lang.view');
    $edit = \Lang::get('lang.edit');
    $delete = \Lang::get('lang.delete');
    $manageLearner = \Lang::get('lang.manage-learners');

    $actionBtn  = "<a href='{$learnerHref}' class='btn btn-sm btn-info mr-1'><i class='fa fa-users'></i> {$manageLearner}</a> ";
    $actionBtn .= "<a href='{$href}' class='btn btn-sm btn-primary mr-1'><i class='fa fa-eye'></i> {$view}</a> ";
    $actionBtn .= "<a href='{$editHref}' class='btn btn-sm btn-warning mr-1'><i class='fa fa-pencil'></i> {$edit}</a> ";

    // Show delete button only if the user is admin or superadmin
  if (in_array($routeSlug, ['admin', 'superadmin'])) {
    $actionBtn .= "<button type='button' class='btn btn-sm btn-danger delete-learning-path' data-href='{$href}' data-role='{$routeSlug}'>
                     <i class='fa fa-trash'></i> {$delete}
                   </button>";

}

    return $actionBtn;
})
->rawColumns(['action'])
->make(true);
        }
        return view('creator.shared.learning-paths.index', compact('routeSlug'));
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::whereNull('parent')->orderBy('id', 'desc')->get();
        $subCategories = Category::whereNotNull('parent')->orderBy('id', 'desc')->get();
        $routeSlug = $this->getRouteSlug();
        return view('creator.shared.learning-paths.create', compact('routeSlug', 'categories', 'subCategories'));
    }

    public function show(Request $request, $id) {
        $source = $request->source;
        $packageId = $request->id;
        $learningPath = LearningPath::with('resources', 'certificate', 'packageLearningPaths.learningPackage')->findOrFail($id);
        $routeSlug = $this->getRouteSlug();
        return view('creator.shared.learning-paths.show', compact('learningPath', 'routeSlug','source', 'packageId'));
    }

    public function edit($id) {
        $categories = Category::whereNull('parent')->orderBy('id', 'desc')->get();
        $learningPath = LearningPath::with('resources')->findOrFail($id);
        $subCategories = Category::where('parent', $learningPath->category_id)->orderBy('id', 'desc')->get();
        $routeSlug = $this->getRouteSlug();
        return view('creator.shared.learning-paths.edit', compact(['learningPath', 'categories', 'subCategories', 'routeSlug']));
    }


    /**
     * Store a newly created learning path in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $imageFormats = implode(',', Config::get("constant.LEARNINGPATH_IMAGE_FORAMTS"));
            request()->validate([
                'name' => 'required|min:4',
                'description' => 'required|min:10',
                'suitable_for' => 'required',
                'language' => 'required',
                'instructor' => 'required',
                'level' => 'required',
                'tags_Keywords' => 'required',
                'type' => 'required',
                'duration' => 'required',
                'image' =>  'mimes:' . $imageFormats . '|max:5120',
                'category' => 'required|exists:categories,id',
                'sub_category' => 'required|exists:categories,id',
                'price' => 'required'
            ]);

            if($request->botOnly != 1){
                request()->validate([
                    "resources"    => "required|array|min:1",
                    "resources.*.title"  => "required|min:1",
                    "resources.*.link"  => "required|min:1",
                    "resources.*.resource_type"  => "required|in:media_link,chatbot_link,course_link"
                ]);
            }

            if($request->botOnly == 1){
                request()->validate([
                    "chatbot" => 'required',
                    "iframe_link" => 'required'
                ]);
            }

            // remove extra comma from string
            $arr = explode(',', $request->tags_Keywords);
            $arr = array_filter($arr);
            $tags = implode(',', $arr);

            $larningPath = new LearningPath;
            $larningPath->name = $request->name;
            $larningPath->description = $request->description;
            $larningPath->category_id = $request->category;
            $larningPath->sub_category_id = $request->sub_category;
            $larningPath->suitable_for = $request->suitable_for;
            $larningPath->language = $request->language;
            $larningPath->instructor = $request->instructor;
            $larningPath->level = $request->level;
            $larningPath->tags_Keywords = $tags;
            $larningPath->type = $request->type;
            $larningPath->requirements = $request->requirements;
            $larningPath->duration = $request->duration;
            $larningPath->price = $request->price;
            $larningPath->unique_ID = $request->type.rand ( 10000 , 99999 );
            $larningPath->uploaded_by = $request->uploaded_by;
            $larningPath->iframe_link = $request->iframe_link ? $request->iframe_link : null;
            $larningPath->chatbot_id = $request->chatbot ? $request->chatbot : null;
            $larningPath->is_only_bot = $request->botOnly ? $request->botOnly : 0;
            $larningPath->bot_code = $request->type.'_'.$request->level.'_'. date("dmY").'_'.rand ( 10000 , 99999 );

            if($request->hasFile('image')) {
                //upload image
                $file = $request->file('image');
                $fileOriginalName = time() . '_' . rand(0,9) . 'learningPathImage';

                // check directory exist or not
                $path = storage_path('app/public' . Config::get('constant.LEARNING_PATH_STORAGE'));
                if(!is_dir($path)) {
                    File::makeDirectory($path, $mode = 0775, true, true);
                }

                $profileLocation = storage_path('app/public'). Config::get('constant.LEARNING_PATH_STORAGE') . '/';
                $uploadFilename = $this->uploadImage($fileOriginalName, $profileLocation, $file);
                $larningPath->featured_image = $uploadFilename;
            }

            $larningPath->save();

            // if chatbot is not 1
            if($request->botOnly != 1)
            {
                foreach($request->resources as $resource) {
                    $resourceLink = new LearningPathResource;
                    $resourceLink->learning_path_id = $larningPath->id;
                    $resourceLink->type = $resource['resource_type'];
                    $resourceLink->title = $resource['title'];
                    if($resourceLink->type != 'course_link') {
                        $resourceLink->link = $resource['link'];
                    } else {
                        $resourceLink->link = "DONOTUSETHIS";
                        $resourceLink->scorm_package_id = $resource['packageId'];
                    }
                    $resourceLink->resource_order = $resource['orderID'];
                    $resourceLink->save();

                    if($resourceLink->type == 'course_link') {
                        $this->moveSCORMFile($resource['packageId'], $larningPath->id, $resourceLink->id);
                    }
                }
            }
             return response()->json(['success' => true, "id" => $larningPath->id], 200);
         //   return redirect('/superadmin/learning-paths')->with('success', 'Learning Path added successfully');
        } catch (Exception $e) {
            Log::error($e);
            if($e instanceof ValidationException) {
                return response()->json(['success' => false, "messsage"=> $e->errors()], 200);
            }
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    /** Update the specified learningPath in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\LearningPath  $learningPath
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $larningPath = LearningPath::findOrFail($id);
            $imageFormats = implode(',', Config::get("constant.LEARNINGPATH_IMAGE_FORAMTS"));
            request()->validate([
                'name' => 'required|min:4',
                'description' => 'required|min:10',
                'suitable_for' => 'required',
                'language' => 'required',
                'instructor' => 'required',
                'level' => 'required',
                'duration' => 'required',
                'description' => 'required|min:10',
                'category' => 'required|exists:categories,id',
                'sub_category' => 'required|exists:categories,id',
                'price' => 'required'
            ]);

            if($request->resources){
                request()->validate([
                    "resources"    => "required|array|min:1",
                    "resources.*.title"  => "required|min:1",
                    "resources.*.link"  => "required|min:1",
                    "resources.*.resource_type"  => "required|in:media_link,chatbot_link,course_link"
                ]);
            }

            if($larningPath->is_only_bot == 1){
                request()->validate([
                    "chatbot" => 'required',
                    "iframe_link" => 'required'
                ]);
            }

            // remove extra comma from string
            $arr = explode(',', $request->tags_Keywords);
            $arr = array_filter($arr);
            $tags = implode(',', $arr);

            $larningPath->name = $request->name;
            $larningPath->description = $request->description;
            $larningPath->category_id = $request->category;
            $larningPath->sub_category_id = $request->sub_category;
            $larningPath->suitable_for = $request->suitable_for;
            $larningPath->language = $request->language;
            $larningPath->instructor = $request->instructor;
            $larningPath->level = $request->level;
            $larningPath->price = $request->price;
            $larningPath->requirements = $request->requirements;
            $larningPath->tags_Keywords = $request->tags_Keywords ? $tags : $larningPath->tags_Keywords;
            $larningPath->duration = $request->duration;
            $larningPath->uploaded_by = $request->uploaded_by;
            $larningPath->chatbot_id = $request->chatbot ? $request->chatbot : $larningPath->chatbot;
            $larningPath->iframe_link = $request->iframe_link ? $request->iframe_link : null;

            if($request->hasFile('image')) {
                //upload image
                $file = $request->file('image');
                $fileOriginalName = time() . '_' . rand(0,9) . 'learningPathImage';
                $profileLocation = storage_path('app/public'). Config::get('constant.LEARNING_PATH_STORAGE') . '/';

                $uploadFilename = $this->uploadImage($fileOriginalName, $profileLocation, $file);
                $larningPath->featured_image = $uploadFilename;

            }

            $larningPath->save();

            if($larningPath->is_only_bot != 1){
                foreach($request->resources as $resourceData) {
                    $resource = null;
                    $shouldMoveCourse = true;
                    if($resourceData['id'] && $resourceData['id'] != 'undefined') {
                        $resource = LearningPathResource::findOrFail($resourceData['id']);
                        if($resource->type != 'course_link' || $resource->scorm_package_id == $resourceData['packageId']) {
                            $shouldMoveCourse = false; // avoid removing as package is not uploaded again
                        }
                    } else {
                        $resource = new LearningPathResource;
                        $resource->learning_path_id = $larningPath->id;
                        $resource->type = $resourceData['resource_type'];
                        $resource->resource_order = $resourceData['orderID'];
                    }
                    $resource->title = $resourceData['title'];
                    if($resource->type != 'course_link') {
                        $resource->link = $resourceData['link'];
                        $shouldMoveCourse = false;
                    } else {
                        $resource->link = "DONOTUSETHIS";
                        $resource->scorm_package_id = $resourceData['packageId'];
                    }

                    $resource->save();

                    // should happen only after resource is saved
                    if($shouldMoveCourse) {
                        $this->moveSCORMFile($resourceData['packageId'], $larningPath->id, $resource->id);
                    }

                }
            }
            return response()->json(['success' => true, "id" => $larningPath->id], 200);
        } catch (Exception $e) {
            Log::error($e);
            if($e instanceof ValidationException) {
                return response()->json(['success' => false, "messsage"=> $e->errors()], 200);
            }
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
     }

    /**
     * Soft delete the specified Learning Path from storage.
     *
     * @param  \App\LearningPath  $learningPath
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {

            // find learning path
            $learningPath = LearningPath::findOrFail($id);

            // remove image from storage
            $learningImagePath = storage_path('app/public'). Config::get('constant.LEARNING_PATH_STORAGE') . '/';
            if(file_exists($learningImagePath.$learningPath->featured_image)){
                unlink($learningImagePath.$learningPath->featured_image);
            }
            $isDeleted = LearningPath::where('id', $id)->update(['status' => false]);
            if($isDeleted == 0){
                return response()->json(['success' => false, "messsage" =>  \Lang::get('lang.generic-error')], 200);
            }
            return response()->json(['success' => true, "messsage" =>  \Lang::get('lang.learningpath-deleted')], 200);

        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    public function fileUploader(Request $request)
    {
        $data = $request->all();
        if($request->type == 'course') {
            $result = $this->uploadSCORM($request);
        } else {
            $result = $this->uploadMedia($request);
        }
        return $result;
    }
    // upload Image
    protected function uploadImage($fileName, $fileLocation, $file)
    {
        $uploadFilename = $fileName .'.'.$file->getClientOriginalExtension();
        $isUploaded = $file->move($fileLocation, $uploadFilename);
        if($isUploaded) {
            return $uploadFilename;
        }
    }

    private function uploadSCORM(Request $request)
    {
        try {
            $scromFormats = implode(',', Config::get("constant.LEARNINGPATH_SCROM_FORAMTS"));
            $request->validate([
                'file' => 'required|mimes:' . $scromFormats. '|max:1048576'
            ]);

            $file = $request->file;
            $fileOriginalName = time() . '_' . rand(0,9) . '_scorm_content';
            $uploadFilename    = $fileOriginalName .'.'.$file->getClientOriginalExtension();

            // check directory exist or not
            $path = storage_path('app/public' . Config::get('constant.SCROM_COURSE'));
            if(!is_dir($path)) {
                File::makeDirectory($path, $mode = 0775, true, true);
            }

            $courseLocation    = storage_path('app/public'). Config::get('constant.SCROM_COURSE');
            $isUpload          = $file->move($courseLocation, $uploadFilename);
            $package_id = '';

            if($isUpload && $fileOriginalName)
            {
                $filename        = $courseLocation . $uploadFilename;
                $extractPath    = $courseLocation . $fileOriginalName;
                $isValid         = Zip::check($filename);

                if($isValid)
                {
                    $zip = Zip::open($filename);
                    $zip->extract($extractPath);
                    $zip->close();
                    $xmlPath  = $extractPath . '/imsmanifest.xml';
                    File::delete($filename);
                    $isExists = File::exists($xmlPath);
                    if($isExists)
                    {
                        // get package Id
                        $package_id = $this->getPackageInfo($xmlPath, $extractPath);
                    } else {
                        throw new Exception("Invalid File");
                    }
                }
            }
            return response()->json(['success' => true, "type"=> "course",  "messsage" => "Successfully Uploaded", "package_id" => $package_id], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    public function getPackageInfo($filePath, $extractPath )
    {

        $xmlString = file_get_contents($filePath);
        $xmlString = str_replace('adlcp:scormtype', 'adlcp_scormtype', $xmlString);

        // Step 2 - parse to string
        $xmlObject = simplexml_load_string($xmlString);

        // Step3 - Convert to Array
        $xmlArray = json_decode(json_encode($xmlObject), true);

        $identifier = $xmlArray['@attributes']['identifier'];

        // meta details
        $versionMeta  = isset($xmlArray['metadata']) ? $xmlArray['metadata'] : NUll;

        if($versionMeta != null) {
            $scormVersion = $xmlArray['metadata']['schemaversion'];
            if($scormVersion != '1.2')
            {
                // return error i.e invalid version of scrom file
                throw new Exception("Unsupported version of SCORM");
            }
        } else {
            $scormVersion = '1.2';
        }

        // organization details
        $organizationsDetail  = $xmlArray['organizations'];
        $organizationsDefault = $organizationsDetail['@attributes']['default'];

        $items = $organizationsDetail['organization']['item'];

        $itemDetails = array ();
        foreach($items as $index => $item) {

            if(isset($item['item'])) {

                if(is_array($item['item'])) {
                    foreach($item['item'] as $index => $childItem) {
                        $itemInfo = new stdClass();
                        $itemInfo->identifierref = $childItem['@attributes']['identifierref'];
                        if(isset($childItem['@attributes']['parameters'])) {
                            $itemInfo->parameters = $childItem['@attributes']['parameters'];
                        }
                        array_push($itemDetails, $itemInfo);
                    }
                }
            } else if(isset($item['identifierref'])) {

                $itemInfo = new stdClass();
                $itemInfo->identifierref = $item['identifierref'];
                if(isset($item['parameters'])) {
                    $itemInfo->parameters = $item['parameters'];
                }
                array_push($itemDetails, $itemInfo);
            }
        }
        $organizations = $organizationsDetail['organization'];

        $scormResources = $xmlArray['resources'];

        // explode path
        $path = (explode("scorm-course/", $extractPath));

        // store scrompackage
        $scormPackage = new ScormPackage();
        $scormPackage->package_id           = $identifier;
        $scormPackage->package_saved_path   = 'scorm-course/' . $path[1];
        $scormPackage->defaultOrg           = $organizationsDefault;
        $scormPackage->scormVersion         = $scormVersion;
        $scormPackage->save();

        // store Items
        $itemDetailsInfos = $this->getItemsInfo($scormResources, $itemDetails);

        foreach($itemDetailsInfos as $itemDetailsInfo) {

            $ScromPackageResourceItem = new ScromPackageResourceItem();
            $ScromPackageResourceItem->item = $itemDetailsInfo->identifierref;
            $ScromPackageResourceItem->href = $itemDetailsInfo->href;
            $ScromPackageResourceItem->parameters = isset($itemDetailsInfo->parameters) ? $itemDetailsInfo->parameters : null;
            $ScromPackageResourceItem->package_id =  $scormPackage->scorm_package_id;
            $ScromPackageResourceItem->save();

        }

        return  $scormPackage->scorm_package_id;
    }

    public function getItemsInfo($scormResources, $itemDetails) {

        $resourcesInfo = array ();
        if(! isset($scormResources['resource']['@attributes'])) {
            foreach ($scormResources['resource'] as $index => $resource) {

                if(isset($resource['@attributes']['href'])) {
                    $resourceInfo = new stdClass();
                    $resourceInfo->identifier = $resource['@attributes']['identifier'];
                    $resourceInfo->type = $resource['@attributes']['type'];
                    $resourceInfo->href = $resource['@attributes']['href'];
                    array_push($resourcesInfo, $resourceInfo);
                }
            }
        } else {

                foreach ($scormResources as $index => $resource) {
                    if(isset($resource['@attributes']['href'])) {
                        $resourceInfo = new stdClass();
                        $resourceInfo->identifier = $resource['@attributes']['identifier'];
                        $resourceInfo->type = $resource['@attributes']['type'];
                        $resourceInfo->href = $resource['@attributes']['href'];
                        array_push($resourcesInfo, $resourceInfo);
                    }
                }
        }

            foreach($itemDetails as $index => $itemDetail) {
                foreach ($resourcesInfo as $index => $resource) {
                        if($itemDetail->identifierref == $resource->identifier) {
                            $itemDetail->href = $resource->href;
                            break;
                        }
                }
            }

            return $itemDetails;
    }

    public function previewCourse($id) {
        $scormItems = ScromPackageResourceItem::where('package_id', $id)->get();
        $scormPackage = ScormPackage::where('scorm_package_id', $id)->first();

        return view('creator.shared.learning-paths.show_learning_path', compact('scormItems', 'scormPackage'));
    }

    private function uploadMedia(Request $request)
    {
        try {

            $mediaFormats = implode(',', Config::get("constant.LEARNINGPATH_MEDIA_FORAMTS"));
            $request->validate([
                'file' => 'required|mimes:' . $mediaFormats. '|max:1048576'
            ]);

            $user = Auth::user();
            $file = $request->file;
            $fileName = time() . '_' . rand(0,9) . '_media_content';
            $uploadFilename = $fileName .'.'.$file->getClientOriginalExtension();

            // check directory exist or not
            $path = storage_path('app/public' . Config::get('constant.LEARNING_PATH_MEDIA'));
            if(!is_dir($path)) {
                File::makeDirectory($path, $mode = 0775, true, true);
            }

            $basePath = storage_path('app/public');
            $folderPath = Config::get('constant.LEARNING_PATH_MEDIA'). time() .'_'. $user->id .'/files/media/' ;

            $path = $basePath. $folderPath;

            File::makeDirectory($path, $mode = 0777, true, true);

            $isUploaded = $file->move($path, $uploadFilename);

            $uploadedMediaPath = $folderPath . $uploadFilename;

            if($isUploaded)
            {
                return response()->json(['success' => true, "type"=> "media", "messsage" => "Suucessfully Uploaded", 'media'=>$uploadedMediaPath], 200);
            }

        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    protected function getRouteSlug()
    {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }

    protected function showSuperadminResponses(Request $request, $id)
    {
        // check condition for user filter or dashboard filter
        $export_countries = empty($request->query('filter_country')) ? 0 : ((gettype($request->query('filter_country')) == 'string') ? $request->query('filter_country') :  (implode(',', $request->query('filter_country'))));
        $export_regions = empty($request->query('filter_region')) ? 0 : ((gettype($request->query('filter_region')) == 'string') ? $request->query('filter_region') :  (implode(',', $request->query('filter_region'))));
        $export_roles = empty($request->query('filter_role')) ? 0 : ((gettype($request->query('filter_role')) == 'string') ? $request->query('filter_role') :  (implode(',', $request->query('filter_role'))));

        $countries = Country::orderBy('name')->get();
        $regions = Region::all();
        $roles = Role::whereNotIn('name', ['superadmin', 'admin'])->get();

        $page = $request->page ? $request->page : 1;
        $country = $request->filter_country;
        $region = $request->filter_region;
        $role = $request->filter_role;
        $search = trim($request->query('search'));
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $query = UserLearningProgress::where('learning_resource_id', $id)->with('learner');

        if(!empty($country) && $country != -1) {
            $query = $query->whereHas('learner', function($q) use($country)
                        {
                            $q->where('country_id', $country);
                        });
        }

        if(!empty($region) && $region != -1) {
            $query = $query->whereHas('learner', function($q) use($region)
                        {
                            $q->where('region_id', $region);
                        });
        }

        if(!empty($role) && $role != -1) {
            $query = $query->whereHas('learner', function($q) use($role)
                        {
                            $q->whereHas('roles', function ($s) use ($role){
                                $s->where("id", $role);
                            });
                        });
        }

        if (!empty($search)) {
            $query = $query->whereHas('learner', function($q) use($search)
                        {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
        }

        $responses = $query->paginate($limit);

        $routeSlug = $this->getRouteSlug();
        return view('creator.shared.learning-paths.learner_response_superadmin', compact(['responses', 'routeSlug', 'regions', 'countries', 'roles', 'export_countries', 'export_regions', 'export_roles', 'id']));
    }

    public function contentwiseProgress($userId, $learningPathId){
        $routeSlug = Auth::user()->getRoleNames()->first();
        return view('creator.shared.learning-paths.learning-path-user-progress', compact('userId', 'learningPathId', 'routeSlug'));
    }

    public function contentwiseProgressRecord(Request $request, $userId, $learningPathId)
    {
        $learningPathProgress = DB::table('learning_path_resources')
        ->leftjoin('user_learning_progress', 'learning_path_resources.id','=',
          DB::raw('user_learning_progress.learning_resource_id AND user_learning_progress.user_id = ' . $userId))
        ->where('learning_path_resources.learning_path_id', '=', $learningPathId)
        ->select('user_learning_progress.id', 'user_learning_progress.lesson_status', 'learning_path_resources.title', 'learning_path_resources.type', 'user_learning_progress.start_date', 'user_learning_progress.end_date', 'user_learning_progress.score', 'user_learning_progress.cmi_data')
        ->get();
        if ($request->ajax())
        {
            return Datatables::of($learningPathProgress, $request)
            ->addIndexColumn()
            ->editColumn('rate', function($learningPathProgress)
            {
                $rate = (is_null($learningPathProgress->end_date)) ? 'N/A' : '100 %';
                return $rate;

            })
            ->editColumn('start_date', function($learningPathProgress)
            {
                $start_date = is_null($learningPathProgress->start_date) ? 'N/A' : date("d/m/Y", strtotime($learningPathProgress->start_date));
                return $start_date;
            })

            ->editColumn('end_date', function($learningPathProgress)
            {
                $end_date = is_null($learningPathProgress->end_date) ? 'N/A' : date("d/m/Y", strtotime($learningPathProgress->end_date));
                return $end_date;
            })
            ->editColumn('duration', function($learningPathProgress)
            {
                if(is_null($learningPathProgress->cmi_data))
                {
                    $duration = strtotime($learningPathProgress->end_date) - (strtotime($learningPathProgress->start_date));
                    return floor($duration / 86400).' days';
                } else {
                    $duration = json_decode($learningPathProgress->cmi_data);
                    return  ($duration->core->session_time);
                }
            })
            ->editColumn('score', function($learningPathProgress)
            {
                return is_null($learningPathProgress->score) ? '0' :  $learningPathProgress->score;
            })
            ->editColumn('action', function ($learningPathProgress){

                if($learningPathProgress->type == 'course_link' ){
                    $viewQuizScore = \Lang::get('lang.view-quiz-score');
                    $actionBtn =  ($learningPathProgress->lesson_status ? ucfirst($learningPathProgress->lesson_status) : \Lang::get('lang.not-attempted'));
                    // $actionBtn = $actionBtn . "  <a href='#'><i class='fa fa-eye' aria-hidden='true'></i> $viewQuizScore </a>";
                    return $actionBtn;

                } else {
                    return ( $learningPathProgress->id ? \Lang::get('lang.completed') : \Lang::get('lang.incomplete'));
                }
            })
            ->make(true);
        }
    }

    protected function showAdminResponses(Request $request, $id)
    {
        // check condition for user filter or dashboard filter
        $export_regions = empty($request->query('filter_region')) ? 0 : ((gettype($request->query('filter_region')) == 'string') ? $request->query('filter_region') :  (implode(',', $request->query('filter_region'))));
        $export_roles = empty($request->query('filter_role')) ? 0 : ((gettype($request->query('filter_role')) == 'string') ? $request->query('filter_role') :  (implode(',', $request->query('filter_role'))));

        $user = Auth::user();
        $userRegions = $user->region_id;
        $explodeRegion = explode(',', $userRegions);
        $regions =  Region::whereIn('id', $explodeRegion)->select('id', 'name')->get();
        $roles = Role::whereNotIn('name', ['superadmin', 'admin'])->get();

        $region = $request->filter_region;
        $role = $request->filter_role;
        $search = trim($request->query('search'));
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $query = UserLearningProgress::where('learning_resource_id', $id)->with('learner')->whereHas('learner', function ($q) use ($explodeRegion){
                        $q->whereIn('region_id', $explodeRegion);
                  });

        if(!empty($region) && $region != -1) {
            $query = $query->whereHas('learner', function($q) use($region)
                        {
                            $q->where('region_id', $region);
                        });
        }

        if(!empty($role) && $role != -1) {
            $query = $query->whereHas('learner', function($q) use($role)
                        {
                            $q->whereHas('roles', function ($s) use ($role){
                                $s->where("id", $role);
                            });
                        });
        }

        if (!empty($search)) {
            $query = $query->whereHas('learner', function($q) use($search)
                        {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
        }

        $responses = $query->paginate($limit);
        $routeSlug = $this->getRouteSlug();
        return view('creator.shared.learning-paths.learner_response_admin', compact(['responses', 'routeSlug', 'regions', 'roles', 'export_regions', 'export_roles', 'id']));

    }

    public function exportLearningPathResponse(Request $request, $id)
    {
        $headings = ['Name', 'Head', 'Group', 'Score', 'Question1', 'Answer1', 'Result1', 'Question2', 'Answer2', 'Result2', 'Question3', 'Answer3', 'Result3', 'Question4', 'Answer4', 'Result4', 'Question5', 'Answer5', 'Result5', 'Question6', 'Answer6', 'Result6', 'Question7', 'Answer7', 'Result7', 'Question8', 'Answer8', 'Result8', 'Question9', 'Answer9', 'Result9', 'Question10', 'Answer10', 'Result10', 'Question11', 'Answer11', 'Result11', 'Question12', 'Answer12', 'Result12', 'Question13', 'Answer13', 'Result13', 'Question14', 'Answer14', 'Result14', 'Question15', 'Answer15', 'Result15', 'Question16', 'Answer16', 'Result16', 'Question17', 'Answer17', 'Result17'];
        return Excel::download(new LearningPathResponseExport($request->country, $request->region, $request->role, $id, $headings), 'learner-response.xls');
    }
}
