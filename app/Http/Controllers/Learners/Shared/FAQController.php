<?php

namespace App\Http\Controllers\Learners\Shared;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ { Faq, FAQCategory };

class FAQController extends Controller
{
    
    public function index(Request $request)
    {
        $faqCategories = FAQCategory::all();
        $routeSlug = $this->getRouteSlug();

        if ($request->ajax())
        {
            $search = trim($request->query('name'));
            $query = Faq::where('status',1);
            if (!empty($search)) {
                $query->where('question', 'like', '%' . $search . '%');
            }
            if (!empty($request->category) && ($request->category != -1)) {
                $query->where('faq_category', 'like', '%' . $request->category . '%');
            }

            $faqs = $query->orderBy('question')->get();
            return Datatables::of($faqs)
            ->addIndexColumn()
            ->make(true);
        }
        return view('learners.shared.faqs.index', compact('routeSlug', 'faqCategories'));
    }

    protected function getRouteSlug()
    {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }

}
