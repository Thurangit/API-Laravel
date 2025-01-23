<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PostController extends Controller
{
    /**
     * Récupérer tous les posts
     * Méthode GET
     */
    public function index()
    {
        $posts = Posts::all();
        return response()->json($posts, 200);
    }

    /**
     * Récupérer un post spécifique
     * Méthode GET
     */
    public function show($id)
    {
        $post = Posts::findOrFail($id);
        return response()->json($post, 200);
    }

    /**
     * Créer un nouveau post
     * Méthode POST
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $post = Posts::create($request->all());
        return response()->json($post, 201);
    }

    /**
     * Mettre à jour complètement un post
     * Méthode PUT
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $post = Posts::findOrFail($id);
        $post->update($request->all());

        return response()->json($post, 200);
    }

    /**
     * Mise à jour partielle d'un post
     * Méthode PATCH
     */
    public function partialUpdate(Request $request, $id)
    {
        $post = Posts::findOrFail($id);

        $post->fill($request->only([
            'titre',
            'contenu'
        ]));

        $post->save();

        return response()->json($post, 200);
    }

    /**
     * Supprimer un post
     * Méthode DELETE
     */
    public function destroy($id)
    {
        $post = Posts::findOrFail($id);
        $post->delete();

        return response()->json(null, 204);
    }
}
