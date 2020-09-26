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

    public function testStore()
    {
        $response = $this->json('POST', route('genres.store'), []);
        $this->assertInvalidationDataRequired($response);

        $data = [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ];

        $response = $this->json('POST', route('genres.store'), $data);

        $this->assertInvalidationDataMax($response);
        $this->assertInvalidationDataBoolean($response);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre]), []);
        $this->assertInvalidationDataRequired($response);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationDataMax($response);
        $this->assertInvalidationDataBoolean($response);
    }

    public function testDelete()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre]));

        $response->assertStatus(204 );

        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response->assertStatus(404 );
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
}
