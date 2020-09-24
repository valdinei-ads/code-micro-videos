<?php

namespace Tests\Feature\Models;

use app\Models\Traits\Uuid;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertEquals(1, $categories->count());
    }

    public function testFilter(){

        Category::create([
            'name' => 'test1',
            'description' => 'description_test1',
            'is_active' => true
        ]);

        $category = Category::all()->firstWhere('name', 'test1');
        $this->assertEquals('test1', $category->name);

        $category = Category::all()->firstWhere('description', 'description_test1');
        $this->assertEquals('description_test1', $category->description);

        $category = Category::all()->firstWhere('is_active', true);
        $this->assertTrue($category->is_active);
    }

    public function testCreate()
    {
        /** @var Category $category */
        $category = Category::create(['name' => 'test1']);
        $category->refresh();
        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $patternUuid = "/^[0-9A-Fa-f]{8}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{12}$/";
        $formatUuidIsCorrect = (boolean)preg_match($patternUuid, $category->getKey());
        $this->assertTrue($formatUuidIsCorrect);

        $category = Category::create([
            'name' => 'test1',
            'description' => null
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test1',
            'description' => 'Test Description'
        ]);
        $this->assertEquals('Test Description', $category->description);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => false
        ]);
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        /** @var Category $category */
        $category = Category::create([
            'name' => 'test_name_category',
            'description' => 'test_description_description',
            'is_active' => false
        ]);
        $data = [
            'name' => 'test_name_category_updated',
            'description' => 'test_description_description_updated',
            'is_active' => true
        ];
        $category->update($data);
        foreach ($data as $key => $value){
            $this->assertEquals($value, $category->{$key});
        }

        //TODO: Criar Teste para atualização do campo created_at
        //$category->update([
        //  'created_at' =>
        //]);

    }

    public function testDelete()
    {
        $category = Category::create(['name' => 'teste']);
        $category->delete();
        $categories = Category::all();
        $this->assertEmpty($categories);
    }

    public function testColumns()
    {
        factory(Category::class, 1)->create();
        $category = Category::all()->first();

        $categoryKeys = array_keys($category->getAttributes());
        $arrayKeysToCompare = [ 'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $this->assertEqualsCanonicalizing($arrayKeysToCompare, $categoryKeys);
    }
}
