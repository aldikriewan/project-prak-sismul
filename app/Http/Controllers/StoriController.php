<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stori;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class StoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('api');
    }

    public function index()
    {
        try {
            $stories = Stori::all();
            Log::info('Fetching all stories');

            return response()->json([
                'success' => true,
                'data' => $stories
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching stories: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $story = Stori::findOrFail($id);
            Log::info('Fetching story with ID: ' . $id);

            return response()->json([
                'success' => true,
                'data' => $story
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Story not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Story not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching story: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store Story Request:', $request->all());

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'description' => 'required|string',
                'story_detail' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imagePath = null;
            $bgImagePath = null;

            if ($request->hasFile('image')) {
                try {
                    if (!$request->file('image')->isValid()) {
                        throw new \Exception('Invalid image file');
                    }
                    $imagePath = $request->file('image')->store('images', 'public');
                    if (!$imagePath) {
                        throw new \Exception('Failed to store image');
                    }
                } catch (\Exception $e) {
                    Log::error('Image upload error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            if ($request->hasFile('background_image')) {
                try {
                    if (!$request->file('background_image')->isValid()) {
                        throw new \Exception('Invalid background image file');
                    }
                    $bgImagePath = $request->file('background_image')->store('images', 'public');
                    if (!$bgImagePath) {
                        throw new \Exception('Failed to store background image');
                    }
                } catch (\Exception $e) {
                    if ($imagePath) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    Log::error('Background image upload error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload background image',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            $story = new Stori();
            $story->title = $validated['title'];
            $story->author = $validated['author'];
            $story->description = $validated['description'];
            $story->story_detail = $validated['story_detail'];
            $story->image = $imagePath;
            $story->background_image = $bgImagePath;

            $story->save();

            Log::info('Story created successfully:', $story->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Story successfully created',
                'data' => $story
            ], 201);

        } catch (ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            if (isset($imagePath)) Storage::disk('public')->delete($imagePath);
            if (isset($bgImagePath)) Storage::disk('public')->delete($bgImagePath);

            Log::error('Store story error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('Update Story Request for ID ' . $id . ':', $request->all());

            $story = Stori::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'description' => 'required|string',
                'story_detail' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                try {
                    if (!$request->file('image')->isValid()) {
                        throw new \Exception('Invalid image file');
                    }
                    
                    // Delete old image if exists
                    if ($story->image) {
                        Storage::disk('public')->delete($story->image);
                    }
                    
                    $imagePath = $request->file('image')->store('images', 'public');
                    if (!$imagePath) {
                        throw new \Exception('Failed to store image');
                    }
                    $story->image = $imagePath;
                } catch (\Exception $e) {
                    Log::error('Image upload error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            if ($request->hasFile('background_image')) {
                try {
                    if (!$request->file('background_image')->isValid()) {
                        throw new \Exception('Invalid background image file');
                    }
                    
                    // Delete old background image if exists
                    if ($story->background_image) {
                        Storage::disk('public')->delete($story->background_image);
                    }
                    
                    $bgImagePath = $request->file('background_image')->store('images', 'public');
                    if (!$bgImagePath) {
                        throw new \Exception('Failed to store background image');
                    }
                    $story->background_image = $bgImagePath;
                } catch (\Exception $e) {
                    Log::error('Background image upload error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload background image',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            $story->title = $validated['title'];
            $story->author = $validated['author'];
            $story->description = $validated['description'];
            $story->story_detail = $validated['story_detail'];
            
            $story->save();

            Log::info('Story updated successfully:', $story->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Story successfully updated',
                'data' => $story
            ], 200);

        } catch (ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Story not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Story not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Update story error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Log::info('Attempting to delete story with ID: ' . $id);
            
            $story = Stori::findOrFail($id);

            // Delete associated images if they exist
            if ($story->image) {
                Storage::disk('public')->delete($story->image);
            }
            if ($story->background_image) {
                Storage::disk('public')->delete($story->background_image);
            }

            $story->delete();

            Log::info('Story deleted successfully with ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Story successfully deleted'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Story not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Story not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Delete story error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete story',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}