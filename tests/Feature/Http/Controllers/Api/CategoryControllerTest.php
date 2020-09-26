<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testStore()
   {
       $response = $this->json('POST', route('categories.store'), []);
       $this->assertInvalidationDataRequired($response);

       $data = [
           'name' => str_repeat('a', 256),
           'is_active' => 'a',
           'description' => str_repeat('a', 651)
       ];

       $response = $this->json('POST', route('categories.store'), $data);

       $this->assertInvalidationDataMax($response);
       $this->assertInvalidationDataBoolean($response);
   }

    public function testUpdate()
   {
       $category = factory(Category::class)->create();

       $response = $this->json('PUT', route('categories.update', ['category' => $category]), []);
       $this->assertInvalidationDataRequired($response);

       $response = $this->json('PUT', route('categories.update', ['category' => $category]), [
           'name' => str_repeat('a', 256),
           'description' => str_repeat('a', 651),
           'is_active' => 'a'
       ]);
       $this->assertInvalidationDataMax($response);
       $this->assertInvalidationDataBoolean($response);
   }

    public function testDelete()
   {
       $category = factory(Category::class)->create();

       $response = $this->json('DELETE', route('categories.destroy', ['category' => $category]));

       $response->assertStatus(204 );

       $response = $this->get(route('categories.show', ['category' => $category->id]));

       $response->assertStatus(404 );
   }

    protected function assertInvalidationDataRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422 )
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

        $response
            ->assertStatus(422 )
            ->assertJsonValidationErrors(['description'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute' => 'description', 'max' => 650])
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
