<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SocietyController;
use App\Http\Controllers\SocietyMemberController;
use App\Http\Controllers\SocietyHeadController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::get('/societies', [SocietyController::class, 'index']);
Route::get('/societies/{id}', [SocietyController::class, 'show']);
Route::get('/societies/{societyId}/members', [SocietyMemberController::class, 'index']);
Route::get('/events/{id}/attendees', [RegistrationController::class, 'eventAttendees']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Events
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::post('/events/{id}/recap', [EventController::class, 'addRecap']);

    // Registrations
    Route::post('/register-event', [RegistrationController::class, 'store']);
    Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations']);

    // Admin - Society Members
    Route::post('/societies/{societyId}/members', [SocietyMemberController::class, 'store']);
    Route::delete('/societies/{societyId}/members/{memberId}', [SocietyMemberController::class, 'destroy']);

    // Society Head
    Route::get('/my-society', [SocietyHeadController::class, 'mySociety']);
    Route::post('/my-society/update', [SocietyHeadController::class, 'updateSociety']);
    Route::post('/my-society/members', [SocietyHeadController::class, 'addMember']);
    Route::delete('/my-society/members/{memberId}', [SocietyHeadController::class, 'removeMember']);
});