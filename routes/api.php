<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes API RESTful pour les posts
Route::prefix('api/v1')->group(function () {
    // Routes pour les posts
    Route::prefix('/posts')->group(function () {
        // Récupérer tous les posts (GET)
        Route::get('/', [PostController::class, 'index']);

        // Récupérer un post spécifique par ID (GET)
        Route::get('/{id}', [PostController::class, 'show']);

        // Créer un nouveau post (POST)
        Route::post('/', [PostController::class, 'store']);

        // Modifier un post existant (PUT)
        Route::put('/{id}', [PostController::class, 'update']);

        // Mise à jour partielle d'un post (PATCH)
        Route::patch('/{id}', [PostController::class, 'partialUpdate']);

        // Supprimer un post (DELETE)
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });
});
