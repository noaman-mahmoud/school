<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/***************************** AuthController Start *****************************/

Route::post('sign-in'              , [App\Http\Controllers\Api\AuthController::class, 'login']);

/***************************** AuthController End *****************************/

Route::group(['middleware' => ['auth:sanctum']], function () {

    /***************************** StudentController Start *****************************/

    Route::get('get-students'       , [App\Http\Controllers\Api\StudentController::class, 'getStudents']);
    Route::get('get-schools'        , [App\Http\Controllers\Api\StudentController::class, 'getSchools']);
    Route::post('store-student'     , [App\Http\Controllers\Api\StudentController::class, 'storeStudent']);
    Route::post('update-student'    , [App\Http\Controllers\Api\StudentController::class, 'updateStudent']);
    Route::post('delete-student'    , [App\Http\Controllers\Api\StudentController::class, 'deleteStudent']);
    Route::get('show-student/{id}'  , [App\Http\Controllers\Api\StudentController::class, 'showStudent']);

    /***************************** StudentController End *****************************/
});

