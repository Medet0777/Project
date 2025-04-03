<?php

use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;

//Public Routes
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

//Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    //User
    Route::get('/user',[AuthController::class,'user']);
    Route::put('/user',[AuthController::class,'update']);
    Route::post('/logout',[AuthController::class,'logout']);

    //Post
    Route::get('/posts',[PostController::class,'index']);
    Route::post('/posts',[PostController::class,'store']);
    Route::get('/posts/{id}',[PostController::class,'show']);
    Route::put('/posts/{id}',[PostController::class,'update']);
    Route::delete('/posts/{id}',[PostController::class,'destroy']);

    //Comment
    Route::get('/posts/{id}/comments',[CommentController::class,'index']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{id}',[CommentController::class,'update']);
    Route::delete('/comments/{id}',[CommentController::class,'destroy']);

    //Like
    Route::post('/posts/{id}/like',[LikeController::class,'likeOrUnlike']);
});


