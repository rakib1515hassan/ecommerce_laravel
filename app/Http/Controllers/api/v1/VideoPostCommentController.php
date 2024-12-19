<?php

namespace App\Http\Controllers\api\v1;


use App\Services\AdditionalServices;
use App\Models\VideoPosts;
use App\Models\VideoPostComments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VideoPostCommentController extends Controller
{
    public function get_customer_comments(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer'
        ]);

        $customer_comments = VideoPostComments::with('created_by_user')->where('post_id', $request->customer_post_id)->get()->map(function ($customer_comment) {
            $customer_comment['created_by'] = $customer_comment['created_by_user']->name;
            $customer_comment['approve_by'] = $customer_comment['created_by_admin']->name;

            unset($customer_comment['created_by_user']);
            unset($customer_comment['created_by_admin']);
            return $customer_comment;
        });

        return response()->json($customer_comments, 200);
    }

    public function get_customer_comment_by_id($id)
    {
        $customer_comment = VideoPostComments::find($id);

        return response()->json($customer_comment, 200);
    }

    public function get_customer_comments_by_customer_post_id($customer_post_id)
    {
        $customer_comments = VideoPostComments::where('customer_id', $customer_post_id)->get();

        return response()->json($customer_comments, 200);
    }

    public function get_customer_comments_by_customer_post_slug($customer_post_slug)
    {
        $customer_post = VideoPosts::where('slug', $customer_post_slug)->first();

        $customer_comments = [];

        if ($customer_post)
            $customer_comments = VideoPostComments::where('customer_id', $customer_post->id)->get();

        return response()->json($customer_comments, 200);
    }

    public function save_post_comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'comment' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }
    
        $customer_post = VideoPosts::find($request->post_id);
        if (!$customer_post) {
            return response()->json(['errors' => ['code' => 1, 'message' => 'The specified post_id does not exist!']], 403);
        }
    
        $customer_comment = new VideoPostComments;
        $customer_comment->post_id = $request->post_id; // Corrected: Assign post_id to the post_id field
        $customer_comment->user_id = Auth::id(); // Assuming you want to store the user_id who made the comment
        $customer_comment->comment = $request->comment;
        // $customer_comment->created_by_id = Auth::id();
        // $customer_comment->is_approved = 0;
    
        $customer_comment->save();
    
        return response()->json($customer_comment, 200);
    }
}
