<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    public function index()
    {
        return response()->json([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
                ->with('likes',function($like){
                    return $like->where('user_id',auth()->user()->id)
                        ->select('id','user_id','post_id')->get();
                })
                ->get()
        ], 200);
    }

    public function store(Request $request){
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image,'posts');

        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image,
        ]);

        return response()->json([
           'message' => 'Post created successfully',
           'post' => $post
        ],200);
    }

    public function show($id){
        return response()->json([
            'post' => Post::where('id',$id)->withCount('comments', 'likes')->get()
        ],200);
    }

    public function update(Request $request,$id){
        $post = Post::find($id);

        if(!$post){
            return response()->json([
               'message' => 'Post not found.'
            ],403);
        }
        if($post->user_id != auth()->user()->id){
            return response()->json([
                'message' => 'Permission denied.'
            ],403);
        }
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' => $attrs['body'],
        ]);


        return response()->json([
            'message' => 'Post updated successfully.',
            'post' => $post
        ],200);
    }

    public function destroy($id){
        $post = Post::find($id);

        if(!$post){
            return response()->json([
                'message' => 'Post not found.'
            ],403);
        }
        if($post->user_id != auth()->user()->id){
            return response()->json([
                'message' => 'Permission denied.'
            ],403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();


        return response()->json([
            'message' => 'Post deleted successfully.',
        ]);
    }
}
