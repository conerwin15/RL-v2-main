<?php

namespace App\Http\Controllers\Api\Shared;

use Log;
use Auth;
use Config;
use Exception;
use App\Events\PointUpdateEvent;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\{ Quiz, Country, QuizQuestion,  QuizSubmission, QuestionOption, UserPointHistory, UserQuiz };


class QuizController extends BaseController
{
public function index(Request $request, $role)
{
    $user = Auth::user();

    if ($request->ajax()) {
        $quizzes = Quiz::with('country')
            ->where(function ($query) use ($user) {
                $query->where('country_id', $user->country_id)
                      ->orWhere('country_id', -1); // ✅ quiz for all countries
            });

        if ($request->name) {
            $quizzes->where('name', 'LIKE', '%' . $request->name . '%');
        }

        return datatables()->of($quizzes)
            ->addIndexColumn()
            ->addColumn('name', fn($q) => $q->name)
            ->addColumn('description', fn($q) => $q->description ?? '')
            ->addColumn('score', fn($q) => $q->score ?? '-')
            ->addColumn('country', fn($q) => $q->country->name ?? 'All') // ✅ display "All" if -1
            ->addColumn('created_on', fn($q) => $q->created_at->format('Y-m-d'))
            ->addColumn('action', function ($row) use ($role) {
                $viewUrl = url("$role/quiz/{$row->id}");
               return '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="Click to view" style="display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 20px;
    text-decoration: none;
    background-color: transparent;
    color: #009dff;
    border: 1px solid lightblue;">
    <i class="fas fa-eye"></i>&nbsp;View
</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('shared.quiz.index', ['routeSlug' => $role]);
}


}
