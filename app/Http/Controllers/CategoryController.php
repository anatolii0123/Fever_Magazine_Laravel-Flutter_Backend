<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;

class CategoryController extends Controller
{
    public function getCategories(Request $request){

        $category = new Category();
        $category = $category->select([
            'id',
            'name',
        ])->get();
        // $responsData = [
        //     'status' => true,
        //     'data' => $category
        // ];
        return response()->json($category);
    }

    public function getCategory($id): JsonResponse
    {

        $category = Category::where('id', $id)
            ->first();

        if (empty($category))
            return response()->json([
                'status' => false,
                'message' => 'No data'
            ]);

        return response()->json([
            'status' => true,
            'message' => 'success',
            'detail' => [
                'id' => $category->id,
                'name' => $category->name,
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createCategory(Request $request): JsonResponse
    {
        $data = $request->only(
            'name',
        );

        $validator = Validator::make($data, [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        $category = Category::create([
            'name' => $data['name'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category is created successfully',
            'detail' => $category
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCategory($id, Request $request): JsonResponse
    {
        $data = $request->only(
            'name',
        );

        $validator = Validator::make($data, [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        $updateCategory = Category::where('id', $id)
            ->update([
                'name' => $data['name'],
            ]);

        if (!$updateCategory)
            return response()->json([
                'status' => false,
                'message' => 'Category is  not updated'
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Category is  updated successfully'
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteCategory($id, Request $request): JsonResponse
    {
        $deleteCategory = Category::where('id', $id)
            ->delete();

        # Check if delete for Category
        if (!$deleteCategory)
            return response()->json([
                'status' => false,
                'message' => 'Category is  not deleted'
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Category is  deleted successfully'
        ]);
    }

}
