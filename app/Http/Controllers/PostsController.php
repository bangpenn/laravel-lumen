<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(title="My First API", version="0.1")
 */

class PostsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="Get list of posts",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */

    public function index()
    {
        $posts = Post::all();

        return response()->json([
            'success' => true,
            'message' =>'List Semua Post',
            'data'    => $posts
        ], 200);
    }

/**
     * @OA\Post(
     *     path="/posts",
     *     summary="Create a new post",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string", example="Post Title"),
     *             @OA\Property(property="content", type="string", example="Post Content")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Semua Kolom Wajib Diisi!',
                'data'   => $validator->errors()
            ],401);

        } else {

            $post = Post::create([
                'title'     => $request->input('title'),
                'content'   => $request->input('content'),
            ]);

            if ($post) {
                return response()->json([
                    'success' => true,
                    'message' => 'Post Berhasil Disimpan!',
                    'data' => $post
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Post Gagal Disimpan!',
                ], 400);
            }

        }
    }

    /**
     * @OA\Get(
     *     path="/posts/{id}",
     *     summary="Get a post by ID",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post Tidak Ditemukan!"),
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $post = Post::find($id);

        if ($post) {
            return response()->json([
                'success'   => true,
                'message'   => 'Detail Post!',
                'data'      => $post
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'message'   => 'Post Tidak Ditemukan!',
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/posts/{id}",
     *     summary="Update a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string", example="Updated Title"),
     *             @OA\Property(property="content", type="string", example="Updated Content")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post updated",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post Tidak Ditemukan!"),
     *         )
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Semua kolom wajib diisi!',
                'data'      => $validator->errors()
            ], 401);
        } else {
            $post = Post::whereId($id)->update([
                'title'     => $request->input('title'),
                'content'   => $request->input('content'),
            ]);

            if ($post) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Post berhasil diupdate!',
                    'data'      => $post
                ], 201);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Post gagal diupdate!'
                ], 400);
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/posts/{id}",
     *     summary="Delete a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post berhasil dihapus!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post tidak ditemukan!")
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        $post = Post::whereId($id)->first();
                    $post->delete();
        
        if ($post) {
            return response()->json([
                'success'   => true,
                'message'   => 'Post berhasil dihapus!',
            ], 200);
        }
    }
}