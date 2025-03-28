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
       
        try {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            
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
                    'path' => url('storage/uploads/' . $imageName)
                ];
                
            }
            return response()->json([
                'status' => true,
                'message' => 'Images uploaded successfully',
                'data' => $uploadedImages
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->implode(' '),
                'data' => null,
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

}
