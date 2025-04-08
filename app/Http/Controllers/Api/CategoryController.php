<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ], 200);
    }

    // Store a new category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string',
            'status' => 'required|in:1,0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false, 
                'message' => collect($validator->errors()->all())->implode(' '), 
                'data' => null
            ], 422);
        }

        try {
            $category = Category::create([
                'name' => $request->name,
                'icon' => $request->icon,
                'status' => $request->status,
                // 'user_id' => auth()->id(), // Removed user-specific assignment
            ]);

            return response()->json([
                'status' => true, 
                'message' => 'Category added successfully!', 
                'data' => $category
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => false, 
                'message' => $e->getMessage(), 
                'data' => null
            ], 500);
        }
    }

    // Show a specific category
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'status' => true, 
                'message' => 'Category retrieved successfully', 
                'data' => $category
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 
                'message' => 'Category not found.', 
                'data' => null
            ], 404);
        }
    }

    // Update a category
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255|unique:categories,name,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => collect($validator->errors()->all())->implode(' '), 
                    'data' => null
                ], 422);
            }

            $category->update(['name' => $request->name]);

            return response()->json([
                'status' => true, 
                'message' => 'Category updated successfully', 
                'data' => $category
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 
                'message' => 'Category not found.', 
                'data' => null
            ], 404);
        }
    }

    // Delete a category
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            $category->delete();

            return response()->json([
                'status' => true, 
                'message' => 'Category deleted successfully', 
                'data' => null
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 
                'message' => 'Category not found.', 
                'data' => null
            ], 404);
        }
    }
}
