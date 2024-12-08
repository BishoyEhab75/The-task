<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function view()
    {
        $tags = Tag::all();
        return TagResource::collection($tags);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }


        $tag = Tag::create([
            'name' => $request->name,
        ]);

        return new TagResource($tag);
    }

    public function update(Request $request, $id)
    {
        
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $tag->update([
            'name' => $request->name,
        ]);

        return new TagResource($tag);
    }

    
    public function delete($id)
    {
        
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }
}
