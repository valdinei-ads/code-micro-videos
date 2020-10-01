<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidationData()
    {
        $response = $this->createGenre([]);
        $this->assertInvalidationDataRequired($response);

        $response = $this->createGenre([
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationDataMax($response);
        $this->assertInvalidationDataBoolean($response);

        $genre = factory(Genre::class)->create();

        $response = $this->updateGenre($genre, []);
        $this->assertInvalidationDataRequired($response);

        $response = $this->updateGenre($genre, [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationDataMax($response);
        $this->assertInvalidationDataBoolean($response);
    }

    protected function assertInvalidationDataRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.required', ['attribute' => 'name'])
            ]);
    }

    protected function assertInvalidationDataMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    protected function assertInvalidationDataBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function testStore()
    {
        $response = $this->createGenre(['name' => 'teste_nome_genre']);

        $genre = Genre::find($response->json('id'));
        $response->assertStatus(201)->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $response = $this->createGenre([
            'name' => 'teste_nome_genre',
            'is_active' => false
        ]);
        $response->assertJsonFragment([
            'name' => 'teste_nome_genre',
            'is_active' => false
        ]);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);
        $response = $this->updateGenre($genre, [
            'name' => 'teste',
            'description' => 'descricao',
            'is_active' => true
        ]);
        $id = $response->json('id');
        $genre = Genre::find($id);
        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true
            ]);
    }

    public function testDelete()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre]));
        $response->assertStatus(204 );

        $response = $this->get(route('genres.show', ['genre' => $genre->id]));
        $response->assertStatus(404 );
    }

    public function createGenre($data)
    {
        $method = 'POST';
        $route = 'genres.store';
        return $this->json($method, route($route), $data);
    }

    public function updateGenre($oldData, $newData)
    {
        $method = 'PUT';
        $route = 'genres.update';
        $oldGenre = ['genre' => $oldData->id];
        return $this->json($method, route($route, $oldGenre), $newData);
    }
}
