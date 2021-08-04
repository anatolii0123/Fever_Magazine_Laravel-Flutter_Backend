<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Models\Comment;

class CommentController extends Controller
{
    public function getComments(Request $request){
        $data = $request->only(
            'story_id',
        );

        $validator = Validator::make($data, [
            'story_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }
        
        $comment = new Comment();
        $comment = $comment->with([
            'user',
        ])
        ->where('story_id', $data['story_id'])
        ->where('status', 'approve')->get()->toArray();

        // foreach($comment as &$item){
        //     $item['child'] = $this->getChildComments($item['id']);
        // }
        $result = [];
        foreach($comment as &$item){

            $myvalue = $item['comment_date'];
            $datetime = new \DateTime($myvalue);

            $temp['comment_id'] = (string)$item['id'];
            $temp['comment_content'] = $item['content'];
            $temp['date'] = $datetime->getTimestamp();
            $temp['user']['name'] = $item['user']['name'];
            $temp['user']['image'] = $item['user']['profile_photo_url'];
            array_push($result, $temp);
            
        }
        $responsData = [
            'data' => $result
        ];
        return response()->json($responsData);
    }

    // public function getChildComments($id){
    //     $child = Comment::where('parent_id', $id)->get()->toArray();
    //     if(empty($child)){
    //         return [];
    //     }else{
    //         foreach($child as &$item){
    //             if(empty(Comment::where('parent_id', $item['id'])->get())){
    //                 $item['child'] = [];
    //             }else{
    //                 $item['child'] = $this->getChildComments($item['id']);
    //             }
    //         }
    //     }
    //     return $child;
    // }

    public function getComment($id): JsonResponse
    {
        $comment = new Comment();
        $comment = $comment->with([
            'user',
            'story',
        ])->where('id', $id)->get();

        // foreach($comment as &$item){
        //     $item['child'] = $this->getChildComments($item['id']);
        // }
        $responsData = [
            'status' => true,
            'data' => $comment
        ];
        return response()->json($responsData);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createComment(Request $request): JsonResponse
    {
        $user = auth()->user();

        $data = $request->only(
            'story_id',
            'comment',
        );

        $validator = Validator::make($data, [
            'story_id' => 'required',
            'comment' => 'string|required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }
        
        $createComment = Comment::create([
            'user_id' => $user->id,
            'story_id' => $data['story_id'],
            'comment_date' => Carbon::now(),
            'type' => 'story',
            'status' => 'approve',
            'content' => $data['comment'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Comment is created successfully',
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateComment($id, Request $request): JsonResponse
    {
        $data = $request->only(
            'comment',
        );

        $validator = Validator::make($data, [
            'comment' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        $updateComment = Comment::where('id', $id)
            ->update([
                'comment' => $data['comment'],
            ]);

        if (!$updateComment)
            return response()->json([
                'status' => false,
                'message' => 'Comment is  not updated'
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Comment is  updated successfully'
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteComment($id, Request $request): JsonResponse
    {
        $deleteComment = Comment::where('id', $id)
            ->delete();

        # Check if delete for Comment
        if (!$deleteComment)
            return response()->json([
                'status' => false,
                'message' => 'Comment is  not deleted'
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Comment is  deleted successfully'
        ]);
    }




}
