<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentRequest;
use App\Http\Requests\PostRequest;
use App\Models\Content;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Retrieve all posts with content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $posts = Post::with('content')->get();
        if($posts->isEmpty()){
            return response()->json([],Response::HTTP_NO_CONTENT); //204
        }
        $postsData = [];
        foreach ($posts as $post) {
            $postData = [
                "id"         => $post->id,
                "title"      => $post->title,
                "short_desc" => $post->short_desc,
                "content"    => $post->content->description
            ];
            array_push($postsData, $postData);
        }
        return response()->json([
            "message" => "Posts retrieved successfully",
            "data" => $postsData
        ], Response::HTTP_OK);//200
    }

    /**
     * Retrieve aspecific post with content by Id
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
    */
    public function show($id){
        $post = Post::with('content')->find($id);
        if(!$post){
            return response()->json([
                "message" => "posts not found"
            ],Response::HTTP_NOT_FOUND); //404
        }
        return response()->json([
            "message" => "post retrieved successfully",
            "data" => [
                "id"         => $post->id,
                "title"      => $post->title,
                "short_desc" => $post->short_desc,
                "content"    => $post->content->description
            ],
        ],Response::HTTP_OK);//200

    }
    /**
     * Store a new post and content
     *
     * @param \App\Http\Request\PostRequest $postRequest
     * @param \App\Http\Request\ContentRequest $contentRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostRequest $postRequest,ContentRequest $contentRequest){

        $validatedPost    = $postRequest->validated();
        $validatedContent = $contentRequest->validated();

        $post = new Post();
        $content = new Content();

        $post->fill($validatedPost);
        $post->save();


        $content->fill($validatedContent);
        $post->content()->save($content);
        $content->save();

        return response()->json([
            "message" => "Data stored successfully",
        ], Response::HTTP_CREATED); // رمز الحالة 201

    }
    /**
     * Update an existing post and content
     *
     * @param \App\Http\Requests\PostRequest $postRequest
     * @param \App\Http\Requests\ContentRequest $contentRequest
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostRequest $postRequest,ContentRequest $contentRequest,$id){
        $post = Post::with('content')->find($id);
        if(!$post){
            return response()->json([
                "message" => "posts not found"
            ],Response::HTTP_NOT_FOUND); //404
        }
        $validatedPost = $postRequest->validated();
        $post->update($validatedPost);
        $validatedContent = $contentRequest->validated();
        if($post->content){
            $post->content->update($validatedContent);
        }else{
            $post->content()->create($validatedContent);
        }

        return response()->json([
            "message" => "Post updated successfully",
            "data" => $post
        ],Response::HTTP_OK);//200
    }

    /**
     * delete an exisiting post
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $post = Post::with('content')->find($id);
        if(!$post){
            return response()->json([
                "message" => "post not found"
            ],Response::HTTP_NOT_FOUND); //404
        }
         $post->delete();
         return response()->json([
            "message" => "post deleted successfully"
         ],Response::HTTP_OK); //200
    }

}
