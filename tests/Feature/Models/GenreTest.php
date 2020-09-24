<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Genre;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $this->assertEquals(1, Genre::all()->count());
    }

    public function testFilter(){

        Genre::create([
            'name' => 'test1',
            'is_active' => true
        ]);

        $genre = Genre::all()->firstWhere('name', 'test1');
        $this->assertEquals('test1', $genre->name);

        $genre = Genre::all()->firstWhere('is_active', true);
        $this->assertTrue($genre->is_active);
    }

    public function testCreate()
    {
        $genre = Genre::create(['name' => 'test1']);
        $genre->refresh();
        $this->assertEquals('test1', $genre->name);
        $this->assertTrue($genre->is_active);

        $patternUuid = "/^[0-9A-Fa-f]{8}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{12}$/";
        $formatUuidIsCorrect = (boolean)preg_match($patternUuid, $genre->getKey());
        $this->assertTrue($formatUuidIsCorrect);

        $genre = Genre::create([
            'name' => 'test1',
            'is_active' => false
        ]);
        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'test1',
            'is_active' => true
        ]);
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = Genre::create([
            'name' => 'test_name_genre',
            'is_active' => false
        ]);
        $data = [
            'name' => 'test_name_genre_updated',
            'is_active' => true
        ];
        $genre->update($data);
        foreach ($data as $key => $value){
            $this->assertEquals($value, $genre->{$key});
        }

        //TODO: Criar Teste para atualização do campo created_at
        //$Genre->update([
        //  'created_at' =>
        //]);

    }

    public function testDelete()
    {
        $genre = Genre::create(['name' => 'teste']);
        $genre->delete();
        $genres = Genre::all();
        $this->assertEmpty($genres);
    }

    public function testColumns()
    {
        factory(Genre::class, 1)->create();
        $genre = Genre::all()->first();

        $genreKeys = array_keys($genre->getAttributes());
        $arrayKeysToCompare = [
            'id',
            'name',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $this->assertEqualsCanonicalizing($arrayKeysToCompare, $genreKeys);
    }
}
