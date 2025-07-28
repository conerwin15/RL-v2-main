<?php

namespace App\Http\Controllers\Api\Shared;

use Log;
use Exception;
use App\Models\{ Faq, FAQCategory };
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

class FaqController extends BaseController
{
    /**
     * Display a listing of the FAQs with search and category filter.
     */
    public function index(Request $request)
{
    try {
        $search = trim($request->search);
        $query = Faq::where('status', 1);

        if (!empty($search)) {
            $words = explode(' ', $search);
            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('question', 'LIKE', '%' . $word . '%');
                }
            });
        }

        if (!empty($request->category) && $request->category != -1) {
            $query->where('faq_category', '=', $request->category);
        }

        return \DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('question', fn($faq) => strip_tags($faq->question))
            ->editColumn('answer', fn($faq) => strip_tags($faq->answer))
            ->make(true);

    } catch (\Exception $e) {
        \Log::error('FAQ Ajax Error: ' . $e->getMessage());
        return response()->json(['error' => true, 'message' => 'Server error.'], 500);
    }
}

    /**
     * Display a listing of the FAQ Categories with search.
     */
    public function faqCategoryList(Request $request)
    {
        try {
            $search = trim($request->searchByCategory);
            $limit = 10;

            $query = FAQCategory::where('status', 1);

            if (!empty($search)) {
                $words = explode(' ', $search);
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->orWhere('faq_category', 'LIKE', '%' . $word . '%');
                    }
                });
            }

            $faqCategories = $query->paginate($limit);

            return $this->sendResponse($faqCategories, \Lang::get('lang.FAQ-category-list'));

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }
    }
}
