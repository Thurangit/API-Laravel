<?php

namespace Tests\Feature;

use App\Models\Posts;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    // Réinitialise la base de données à chaque test
    use RefreshDatabase;

    // Utilisateur authentifié pour les tests
    protected $user;

    // Préparation avant chaque test
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test de récupération de tous les posts
     */
    public function test_peut_recuperer_tous_les_posts()
    {
        // Créer quelques posts
        Posts::factory()->count(3)->create();

        // Requête API
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/v1/posts');

        // Assertions
        $response
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test de création d'un post
     */
    public function test_peut_creer_un_post()
    {
        $postData = [
            'titre' => 'Titre de test',
            'contenu' => 'Contenu de test'
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/posts', $postData);

        $response
            ->assertStatus(201)
            ->assertJson($postData);

        // Vérifier en base de données
        $this->assertDatabaseHas('posts', $postData);
    }

    /**
     * Test de récupération d'un post spécifique
     */
    public function test_peut_recuperer_un_post_specifique()
    {
        $post = Posts::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/posts/{$post->id}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'titre' => $post->titre,
                'contenu' => $post->contenu
            ]);
    }

    /**
     * Test de mise à jour d'un post
     */
    public function test_peut_mettre_a_jour_un_post()
    {
        $post = Posts::factory()->create();

        $updateData = [
            'titre' => 'Titre mis à jour',
            'contenu' => 'Contenu mis à jour'
        ];

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/v1/posts/{$post->id}", $updateData);

        $response
            ->assertStatus(200)
            ->assertJson($updateData);

        // Vérifier la mise à jour en base
        $this->assertDatabaseHas('posts', $updateData);
    }

    /**
     * Test de suppression d'un post
     */
    public function test_peut_supprimer_un_post()
    {
        $post = Posts::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(204);

        // Vérifier la suppression en base
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /**
     * Test de validation des données lors de la création
     */
    public function test_echec_creation_post_sans_titre()
    {
        $postData = [
            'contenu' => 'Contenu sans titre'
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/posts', $postData);

        $response
            ->assertStatus(400)
            ->assertJsonValidationErrors(['titre']);
    }
}
