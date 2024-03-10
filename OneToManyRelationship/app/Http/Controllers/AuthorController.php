<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Requests\PostRequest;
use App\Models\Author;
use Illuminate\Http\Response;

class AuthorController extends Controller
{



    /**
     * Retrieve all author with posts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

        $authors = Author::with(['posts' => function($query){
            $query->select(['title','description','author_id']);
        }])->get(['id','name']);

        //An other way
        //$authors = Author::with('posts:title,description,author_id')->get(['id','name']);
        if($authors->isEmpty()){
            return response()->json([],Response::HTTP_NO_CONTENT);//204
        }

            return response()->json([
                'message' => 'The authors and their contributions were successfully retrieved',
                'data' => $authors], Response::HTTP_OK); //200
        }

        /**
         * Retrieve aspecific author with posts by Id
         *
         * @param int $id
         * @return \Illuminate\Http\JsonResponse
         */
        public function show($id){
            $author = Author::select('id', 'name')->with('posts:author_id,title,description')->find($id);
            if(!$author){
                return response()->json([
                    'message'=> 'None author'
                ],Response::HTTP_NO_CONTENT);//204
            }
            return response()->json([
                'message' => 'The authors and their contributions were successfully retrieved',
                'data' => $author], Response::HTTP_OK); //200
        }

    /**
     * Store the author with all his posts.
     *
     * @param \App\Http\Requests\AuthorRequest $authorRequest
     * @param \App\Http\Requests\PostRequest $postRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AuthorRequest $authorRequest,PostRequest $postRequest){

        $authorValidated =  $authorRequest->validated();
        $postValidated = $postRequest->validated();

        $author = Author::where('name',$authorRequest->name)->first();
        if(!$author){
           $author = Author::create($authorValidated);
        }
        foreach($postValidated['posts'] as $post){
            $author->posts()->create($post);
        }
        return response()->json([
            'message' => 'Data stored successfully'
        ],Response::HTTP_CREATED);//201
    }

    /**
     * Update an existing author and posts
     *
     * @param \App\Http\Requests\AuthorRequest $authorRequest
     * @param \App\Http\Requests\PostRequest $postRequest
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AuthorRequest $authorRequest, PostRequest $postRequest, $id){
        $postCreated=[];
        $postUpdated=[];
        $author = Author::with('posts')->find($id);

        if (!$author) {
            return response()->json([
                'message' => 'None author'
            ], Response::HTTP_NO_CONTENT); //204
        }

        $authorValidated = $authorRequest->validated();
        $author->update($authorValidated);
        $postValidated = $postRequest->validated();

        foreach ($postValidated['posts'] as $postData) {
            if(isset($postData['id'])){
                $post = $author->posts->find($postData['id']);
                if ($post) {
                    if($post->update($postData)){
                        $postUpdated[] = $post->refresh();
                    }
                } else {
                       $postCreated[] = $author->posts()->create($postData);
                    }
                }
            }

        return response()->json([
            "message" => "Author and posts updated successfully",
            "posts updated" => $postUpdated,
            "posts created" => $postCreated,
        ], Response::HTTP_OK); //200
    }


    /**
     * delete an exisiting author and posts
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $author = Author::with('posts')->find($id);
        if(!$author){
            return response()->json([
                "message" => "None author"
            ],Response::HTTP_NO_CONTENT); //204
        }
        $author->delete();
        return response()->json([
            "message" => "author and posts deleted successfully"
        ], Response::HTTP_OK); //200
    }
    
    
}
