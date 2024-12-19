<?php

namespace App\Http\Controllers\Seller;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\QuestionAnswering;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices;

class QAController extends Controller
{
    public function index(Request $request)
    {
        $qas = QuestionAnswering::select('question_answerings.id as id', 'products.name', 'question_answerings.question as question', 'question_answerings.status as status')
            ->join('products', 'products.id', '=', 'question_answerings.product_id')
            ->where('products.user_id', auth('seller')->user()->id);
        if ($request->get('search') !== null) {
            $qas = QuestionAnswering::select('question_answerings.id as id', 'products.name', 'question_answerings.question as question', 'question_answerings.status as status')
                ->join('products', 'products.id', '=', 'question_answerings.product_id')
                ->where('products.name', "like", "%" . $request->get('search') . "%")
                ->orWhere('question_answerings.question', "like", "%" . $request->get('search') . "%")
                ->where('products.user_id', auth('seller')->user()->id);
        } else {
            $qas = $qas->orderBy('question_answerings.id', 'desc');
        }


        $qas = $qas->paginate(AdditionalServices::pagination_limit());
        return view('seller-views.qa.index', compact('qas'));
    }

    public function show($id)
    {
        $qa = QuestionAnswering::find($id);
        return view('seller-views.qa.show', compact('qa'));
    }

    public function reply(Request $request, $questionId)
    {
        $question = QuestionAnswering::find($questionId);
        $question->answer = $request->answer;
        $question->status = 'read';
        $question->answered_by = auth('admin')->user() ? auth('admin')->user()->id : auth('seller')->user()->id;
        $question->awswered_by_admin = auth('admin')->user() ? 1 : 0;
        $question->save();
        return back()->with('success', 'Replied Success!');
    }
}
