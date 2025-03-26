<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Images;

class ImageUploadController extends Controller
{
    public function uploadImages(Request $request)
    {
        // echo "Hello";
        //     exit();
        try {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            // echo "Hello";
            // exit();

            $uploadedImages = [];

            foreach ($request->file('images') as $imageFile) {
                
                $imageName = $imageFile->getClientOriginalName(); // Unique name

                // Store in storage/app/public/uploads
                $image_path = $imageFile->storeAs('uploads', $imageName, 'public');

                // Save image details to database
                $image = new Images();
                $image->image_name = $imageName;
                $image->image_path = 'uploads/' . $imageName;
                $image->save();

                // Add to response array
                $uploadedImages[] = [
                    'name' => $image->image_name,
                    'path' => asset('storage/uploads/' . $imageName) 
                ];
                
            }
            return response()->json([
                'message' => 'Images uploaded successfully',
                'images' => $uploadedImages
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred',
                'data' => $e->getMessage()
            ], 500);
        }
    }


    // public function getImages()
    // {
    //     $images = Images::all();

    //     return response()->json([
    //         'images' => $images
    //     ], 200);
    // }
}
