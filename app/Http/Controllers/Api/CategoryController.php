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

    // Get all categories
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ], 200);
    }


    public function store(Request $request)
{
    // Validate request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255|unique:categories,name',
        'icon' => 'nullable|string',
        'status' => 'nullable|in:1,0',
    ]);
 
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => collect($validator->errors()->all())->implode(' '),
            'data' => null,
        ], 422);
    }
 
    try {
        // Create category with all fields
        $category = Category::create([
            'name' => $request->name,
            'icon' => $request->icon,  // Store icon properly
            'status' => $request->status ?? 1,
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
            'data' => null,
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
                'data' => $category,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found, please provide a valid ID',
                'data' => null,
            ], 404);
        }
    }

    // Update a category
    public function update(Request $request, $id)
    {
        try {
            // Find the category or throw an exception
            $category = Category::findOrFail($id);

            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => collect($validator->errors()->all())->implode(' '),
                    'data' => null,
                ], 422);
            }

            // Update category
            $category->update(['name' => $request->name]);

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found, please provide a valid ID',
                'data' => null,
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
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
                'data' => null,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found, please provide a valid ID',
                'data' => null,
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}

