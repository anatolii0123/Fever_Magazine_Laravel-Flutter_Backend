<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

use Carbon\Carbon;
use App\Models\Story;
use App\Models\Category;

class StoryController extends Controller
{

    public function getAllStories(Request $request){
        $category = new Category();
        $story = new Story();
        $categoryList = $category->orderBy('name', 'ASC')->limit(5)->get()->toArray();

        $result = [];
        foreach($categoryList as &$categoryItem){
            if($categoryItem['name'] == "Uncategorized")
            continue;
            $temp['category'] = $categoryItem['name'];
            $temp['category_id'] = $categoryItem['id'];
            $temp['data'] = [];
            $storyList = $story->select(
                [
                    'id', 
                    'title', 
                    'content', 
                    'featured_image', 
                    'view_count', 
                    'view_count', 
                    'story_date', 
                    'card_type', 
                    'card_link', 
                    'user_id', 
                    'category_id'
                ])->with([
                'user',
                'category',
            ])->where('category_id', $categoryItem['id'])->get()->toArray();
            foreach($storyList as &$storyItem){
                $myvalue = $storyItem['story_date'];
                $datetime = new \DateTime($myvalue);

                $storyItem['date'] = $datetime->format('F j, Y');
                $storyItem['time'] = $datetime->getTimestamp();

                $storyItem['story_card']['type'] = $storyItem['card_type'];
                $storyItem['story_card']['link'] = $storyItem['card_link'];

                $storyItem['user']['image'] = $storyItem['user']['profile_photo_url'];
                $storyItem['user']['is_friend'] = $storyItem['user']['is_friend'] == 1?true:false;

                $item['id'] = $storyItem['id'];
                $item['title'] = $storyItem['title'];
                $item['content'] = $storyItem['content'];
                $item['featured_image'] = $storyItem['featured_image'];
                $item['story_card'] = $storyItem['story_card'];
                $item['view_count'] = (string)$storyItem['view_count'];
                $item['date'] = $storyItem['date'];
                $item['time'] = $storyItem['time'];
                $item['tag'] = []; //for now
                $item['user']['id'] = (string)$storyItem['user']['id'];
                $item['user']['name'] = $storyItem['user']['name'];
                $item['user']['image'] = $storyItem['user']['image'];
                $item['user']['is_friend'] = $storyItem['user']['is_friend'];

                array_push($temp['data'], $item);
            }

            $result[$categoryItem['name']] = $temp;
        }
        return response()->json($result);
    }

    public function getSimilarStories(Request $request){

        $user = auth()->user();
        $data = $request->only(
            'story_id',
            'category_id',
        );

        $validator = Validator::make($data, [
            'story_id' => 'required',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        $story = new Story();

        $category = new Category();

        $result = [];
        $storyList = $story->select(
            [
                'id', 
                'title', 
                'content', 
                'featured_image', 
                'view_count', 
                'view_count', 
                'story_date', 
                'card_type', 
                'card_link', 
                'user_id', 
                'category_id'
            ])->with([
            'user',
            'category',
        ])->where('category_id', $data['category_id'])
        ->where('id', '!=', $data['story_id'])
        ->orderBy('story_date', 'ASC')->limit(20)->get()->toArray();
        foreach($storyList as &$storyItem){
            $myvalue = $storyItem['story_date'];
            $datetime = new \DateTime($myvalue);

            $storyItem['date'] = $datetime->format('F j, Y');
            $storyItem['time'] = $datetime->getTimestamp();

            $storyItem['story_card']['type'] = $storyItem['card_type'];
            $storyItem['story_card']['link'] = $storyItem['card_link'];

            $storyItem['user']['image'] = $storyItem['user']['profile_photo_url'];
            $storyItem['user']['is_friend'] = $storyItem['user']['is_friend'] == 1?true:false;

            $item['id'] = $storyItem['id'];
            $item['title'] = $storyItem['title'];
            $item['content'] = $storyItem['content'];
            $item['featured_image'] = $storyItem['featured_image'];
            $item['story_card'] = $storyItem['story_card'];
            $item['view_count'] = (string)$storyItem['view_count'];
            $item['date'] = $storyItem['date'];
            $item['time'] = $storyItem['time'];

            $categoryInfo = $category->where('id', $storyItem['category_id'])->get()->toArray();
            $categoryInfo[0]['id'] = (string)$categoryInfo[0]['id'];

            $item['tag'] = [$categoryInfo[0]]; //for now
            $item['user']['id'] = (string)$storyItem['user']['id'];
            $item['user']['name'] = $storyItem['user']['name'];
            $item['user']['image'] = $storyItem['user']['image'];
            $item['user']['is_friend'] = $storyItem['user']['is_friend'];

            array_push($result, $item);
        }

        return response()->json($result);
    }

    public function getStoriesByCategory(Request $request){

        $user = auth()->user();
        $data = $request->only(
            'category_id',
        );

        $validator = Validator::make($data, [
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        $story = new Story();

        $category = new Category();

        $result = [];
        $storyList = $story->select(
            [
                'id', 
                'title', 
                'content', 
                'featured_image', 
                'view_count', 
                'view_count', 
                'story_date', 
                'card_type', 
                'card_link', 
                'user_id', 
                'category_id'
            ])->with([
                'user',
                'category',
            ])->where('category_id', $data['category_id'])
            ->orderBy('story_date', 'ASC')->limit(20)->get()->toArray();
        foreach($storyList as &$storyItem){
            $myvalue = $storyItem['story_date'];
            $datetime = new \DateTime($myvalue);

            $storyItem['date'] = $datetime->format('F j, Y');
            $storyItem['time'] = $datetime->getTimestamp();

            $storyItem['story_card']['type'] = $storyItem['card_type'];
            $storyItem['story_card']['link'] = $storyItem['card_link'];

            $storyItem['user']['image'] = $storyItem['user']['profile_photo_url'];
            $storyItem['user']['is_friend'] = $storyItem['user']['is_friend'] == 1?true:false;

            $item['id'] = $storyItem['id'];
            $item['title'] = $storyItem['title'];
            $item['content'] = $storyItem['content'];
            $item['featured_image'] = $storyItem['featured_image'];
            $item['story_card'] = $storyItem['story_card'];
            $item['view_count'] = (string)$storyItem['view_count'];
            $item['date'] = $storyItem['date'];
            $item['time'] = $storyItem['time'];

            $categoryInfo = $category->where('id', $storyItem['category_id'])->get()->toArray();
            $categoryInfo[0]['id'] = (string)$categoryInfo[0]['id'];

            $item['tag'] = [$categoryInfo[0]]; //for now
            $item['user']['id'] = (string)$storyItem['user']['id'];
            $item['user']['name'] = $storyItem['user']['name'];
            $item['user']['image'] = $storyItem['user']['image'];
            $item['user']['is_friend'] = $storyItem['user']['is_friend'];

            array_push($result, $item);
        }

        return response()->json($result);
    }

    public function getLatestStories(Request $request){

        $story = new Story();

        $category = new Category();

        $result = [];
        $storyList = $story->select(
            [
                'id', 
                'title', 
                'content', 
                'featured_image', 
                'view_count', 
                'view_count', 
                'story_date', 
                'card_type', 
                'card_link', 
                'user_id', 
                'category_id'
            ])->with([
            'user',
            'category',
        ])->orderBy('story_date', 'ASC')->limit(20)->get()->toArray();
        foreach($storyList as &$storyItem){
            $myvalue = $storyItem['story_date'];
            $datetime = new \DateTime($myvalue);

            $storyItem['date'] = $datetime->format('F j, Y');
            $storyItem['time'] = $datetime->getTimestamp();

            $storyItem['story_card']['type'] = $storyItem['card_type'];
            $storyItem['story_card']['link'] = $storyItem['card_link'];

            $storyItem['user']['image'] = $storyItem['user']['profile_photo_url'];
            $storyItem['user']['is_friend'] = $storyItem['user']['is_friend'] == 1?true:false;

            $item['id'] = $storyItem['id'];
            $item['title'] = $storyItem['title'];
            $item['content'] = $storyItem['content'];
            $item['featured_image'] = $storyItem['featured_image'];
            $item['story_card'] = $storyItem['story_card'];
            $item['view_count'] = (string)$storyItem['view_count'];
            $item['date'] = $storyItem['date'];
            $item['time'] = $storyItem['time'];

            $categoryInfo = $category->where('id', $storyItem['category_id'])->get()->toArray();
            $categoryInfo[0]['id'] = (string)$categoryInfo[0]['id'];

            $item['tag'] = [$categoryInfo[0]]; //for now
            $item['user']['id'] = (string)$storyItem['user']['id'];
            $item['user']['name'] = $storyItem['user']['name'];
            $item['user']['image'] = $storyItem['user']['image'];
            $item['user']['is_friend'] = $storyItem['user']['is_friend'];

            array_push($result, $item);
        }

        return response()->json($result);
    }

    public function increaseViewCount(Request $request): JsonResponse
    {
        $data = $request->only(
            'id',
        );

        $validator = Validator::make($data, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        Story::where('id', $data['id'])->increment('view_count');

        return response()->json([
            'status' => true,
            'message' => 'Count Increase',
        ]);
    }

    public function getStories(Request $request){

        $story = new Story();
        $story = $story->with([
            'user',
            'category',
        ])->get();
        $responsData = [
            'status' => true,
            'data' => $story
        ];
        return response()->json($responsData);
    }

    public function getStory($id): JsonResponse
    {

        $story = Story::with([
            'user',
            'category',
        ])->where('id', $id)
            ->first();

        if (empty($story))
            return response()->json([
                'status' => false,
                'message' => 'No data'
            ]);

        return response()->json([
            'status' => true,
            'message' => 'success',
            'detail' => $story
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createStory(Request $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->only(
            'title',
            'content',
            'featured_image',
            'card_type',
            'card_link',
            'category_id',
        );

        $validator = Validator::make($data, [
            'title' => 'required|string',
            'content' => 'required|string',
            'featured_image' => 'required|string',
            'card_type' => 'required|string',
            'card_link' => 'required|string',
            'category_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        $story = Story::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'featured_image' => $data['featured_image'],
            'card_type' => $data['card_type'],
            'card_link' => $data['card_link'],
            'category_id' => $data['category_id'],
            'likes' => 0,
            'view_count' => 0,
            'story_date' => Carbon::now(),
            'status' => 'draft',
            'user_id' => $user->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Story is created successfully',
            'detail' => $story
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStory($id, Request $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->only(
            'title',
            'content',
            'featured_image',
            'card_type',
            'card_link',
            'category_id',
            'likes',
            'view_count',
            'status',
        );

        $validator = Validator::make($data, [
            'title' => 'required|string',
            'content' => 'required|string',
            'featured_image' => 'required|string',
            'card_type' => 'required|string',
            'card_link' => 'required|string',
            'category_id' => 'required|string',
            'likes' => 'required|string',
            'view_count' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        $story = Story::where('id', $id)->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'featured_image' => $data['featured_image'],
            'card_type' => $data['card_type'],
            'card_link' => $data['card_link'],
            'category_id' => $data['category_id'],
            'likes' => $data['likes'],
            'view_count' => $data['view_count'],
            'story_date' => Carbon::now(),
            'status' => $data['status'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Story is created successfully',
            'detail' => $story
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteStory($id, Request $request): JsonResponse
    {
        $deleteStory = Story::where('id', $id)
            ->delete();

        # Check if delete for Story
        if (!$deleteStory)
            return response()->json([
                'status' => false,
                'message' => 'Story is  not deleted'
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Story is  deleted successfully'
        ]);
    }


}
