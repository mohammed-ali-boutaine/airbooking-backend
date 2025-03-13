<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use PDOException;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $tags = Tag::get();
            return response()->json(["tags" => $tags], 200);
        } catch (PDOException $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
     * Search products by name
     */

    public function search(string $name)
    {
        return Tag::where('name', 'like', '%' . $name . '%')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // $path = $request->file('icon_path')->store('uploads');

            $tag = Tag::create([
                "name" => $request->name,
                // "icon_path" => $path
            ]);

            return response()->json([
                "tag" => $tag,
                'message' => 'Tag created successfully'
            ], 201);
        } catch (PDOException $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $tag = Tag::findOrFail($id);
            return response()->json(["tag" => $tag], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Tag not found"], 404);
        } catch (PDOException $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon_path' => 'sometimes|nullable|string|max:255',
        ]);

        try {
            $tag = Tag::findOrFail($id);
            $tag->update($request->all());
            return response()->json(["tag" => $tag], 200);
        } catch (PDOException $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return response()->json(["message" => "Tag deleted successfully"], 200);
        } catch (PDOException $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
