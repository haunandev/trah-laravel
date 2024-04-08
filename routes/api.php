<?php

use App\Http\Controllers\Api\MappingRoleTaskController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductDetailController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/sync-data', [PersonController::class, 'syncData']);
$common_route = [
    // users
    [
        'prefix' => 'users',
        'controller' => UserController::class,
    ],
    // roles
    [
        'prefix' => 'roles',
        'controller' => RoleController::class,
    ],
    // tasks
    [
        'prefix' => 'tasks',
        'controller' => TaskController::class,
    ],
    // mapping_role_tasks
    [
        'prefix' => 'mapping_role_tasks',
        'controller' => MappingRoleTaskController::class,
    ],
    // persons
    [
        'prefix' => 'persons',
        'controller' => PersonController::class,
    ],
];
Route::group([
    'middleware' => 'api'
], function ($router) use ($common_route) {
    // Authentication
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::post('me', 'me');
    });
    // common route
    foreach ($common_route as $cr) {
        Route::prefix($cr['prefix'])->controller($cr['controller'])->group(function() use($cr) {
            Route::get('dataset', 'dataset');
            Route::get('list', 'list');
            Route::post('create', 'create');
            Route::get('{id}/show', 'show');
            Route::put('update', 'update');
            Route::delete('delete', 'delete');
            if (isset($cr['custom'])) {
                foreach (($cr['custom'] ?? []) as $custom) {
                    $customMethod = $custom['method'];
                    Route::$customMethod($custom['url'], $custom['function']);
                }
            }
        });
    }
});
