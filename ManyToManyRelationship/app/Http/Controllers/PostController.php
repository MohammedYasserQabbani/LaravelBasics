<?php

namespace App\Http\Controllers;

use App\Http\Requests\categoryRequest;
use App\Http\Requests\postRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * View all posts and their categories
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $posts = Post::with('categories:name')->get(['id','title','body']);

        if(!$posts){
            return response()->json([],JsonResponse::HTTP_NO_CONTENT);//204
        }
        return response()->json([
            'data' => $posts],JsonResponse::HTTP_OK); //200
    }
    /**
     * View the post and its categories
     * 
     * @param int $id
     * @return  \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $post = Post::with('categories:name')->select('id', 'title', 'body')->find($id);
        if (!$post) {
            return response()->json([], JsonResponse::HTTP_NOT_FOUND); // 404
        }
        return response()->json([
            'data' => $post
        ], JsonResponse::HTTP_OK); // 200
    }
    
    /**
     * Store the post and its categories
     * 
     * @param \App\Http\Requests\postRequest $postRequest
     * @param \App\Http\Requests\categoryRequest $categoryRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(postRequest $postRequest, categoryRequest $categoryRequest)
    {
       
        $postValidated = $postRequest->validated();
        $categoryValidated = $categoryRequest->validated();
        try {
            DB::beginTransaction();
            foreach ($categoryValidated['categories'] as $category) {
                Category::firstOrCreate(['name' => $category['name']]);
            }

            $post = Post::create($postValidated);

            $categoryNames = array_column($categoryValidated['categories'], 'name');
            $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

            $post->categories()->attach($categoryIds);

            DB::commit();
            return response()->json([
                'message' => 'Data stored successfully',
                'post' => $post,
                'categories' => $categoryValidated
            ], JsonResponse::HTTP_CREATED); // 201
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to store data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR); // 500
        }
    }
    /**
     * Update the post and its categories
     * 
     * @param \App\Http\Requests\postRequest $postRequest
     * @param \App\Http\Requests\categoryRequest $categoryRequest
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(postRequest $postRequest, categoryRequest $categoryRequest,$id){
       
        $postValidated = $postRequest->validated();
        $categoryValidated = $categoryRequest->validated();
        $post = Post::with('categories:name')->find($id);
        if (!$post) {
            return response()->json([], JsonResponse::HTTP_NOT_FOUND); // 404
        }
        try{
             $post->update($postValidated);
             foreach ($categoryValidated['categories'] as $category) {
                Category::updateOrCreate(['name' => $category['name']]);
            }
            $categoryNames = array_column($categoryValidated['categories'], 'name');
            $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();
    
            $post->categories()->sync($categoryIds);
            DB::commit();
            return response()->json([
                'message' => 'Data updated successfully',
                'post' => $post->only(['id','title','body']),
                'categories' => $categoryValidated
            ], JsonResponse::HTTP_OK); // 200
        }catch (\Exception $e) {
            DB::rollBack();
            return new JsonResponse([
                'message' => 'Failed to delete data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR); // 500
        }
       
    }
    /**
     * Delete post and its categories
     * 
     * @param  int $id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $post = Post::with('categories:name')->find($id);
        if (!$post) {
            return response()->json([], JsonResponse::HTTP_NOT_FOUND); // 404
        }
        try{
            DB::beginTransaction();
            $post->categories()->detach(); 
            $post->delete(); 
            DB::commit();
            return response()->json([
                'message' => 'delete post and its categories'
            ], JsonResponse::HTTP_OK); //200
        }catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Failed to delete data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR); // 500
        }
        
    }
    
        
}
