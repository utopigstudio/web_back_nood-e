<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NoodeEntityController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SetPasswordController;
use App\Http\Controllers\TopicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/invitation/{user}', [NoodeEntityController::class, 'invitation'])->name('invitation');

Route::get('/events', [EventController::class, 'index']);
Route::get('/event{event}', [EventController::class, 'show']);
Route::post('/event', [EventController::class, 'store']);
Route::put('/event/{event}', [EventController::class, 'update']);
Route::delete('/events/{event}', [EventController::class, 'destroy']);

Route::get('/disscussions', [DiscussionController::class, 'index']);
Route::get('/discussion/{discussion}', [DiscussionController::class, 'show']);
Route::post('/discussion', [DiscussionController::class, 'store']);
Route::put('/discussion/{discussion}', [DiscussionController::class, 'update']);
Route::delete('/discussion/{discussion}', [DiscussionController::class, 'destroy']);

Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/room/{room}', [RoomController::class, 'show']);
Route::post('/room', [RoomController::class, 'store']);
Route::put('/room/{room}', [RoomController::class, 'update']);
Route::delete('/room/{room}', [RoomController::class, 'destroy']);

Route::get('/topics', [TopicController::class, 'index']);
Route::get('/topic/{topic}', [TopicController::class, 'show']);
Route::post('/topic', [TopicController::class, 'store']);
Route::put('/topic/{topic}', [TopicController::class, 'update']);
Route::delete('/topic/{topic}', [TopicController::class, 'destroy']);

Route::get('/comments', [CommentController::class, 'index']);
Route::get('/comment/{comment}', [CommentController::class, 'show']);
Route::post('/comment', [CommentController::class, 'store']);
Route::put('/comment/{comment}', [CommentController::class, 'update']);
Route::delete('/comment/{comment}', [CommentController::class, 'destroy']);

Route::group(['middleware' => ['auth']], function () {
    Route::resource('/entities', NoodeEntityController::class);
    Route::post('/set-password', [SetPasswordController::class, 'setPassword'])->name('set-password');
});