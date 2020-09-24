<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use app\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class GenreTest extends TestCase
{
    public function testFillableAttributes()
    {
        $fillable = ['name', 'is_active'];
        $genre = new Genre();
        $this->assertEquals($fillable, $genre->getFillable());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $genreTraits);
    }

    public function testCast()
    {
        $casts = [
            'id' => 'string',
            'is_active' => 'boolean'
        ];
        $genre = new Genre();
        $this->assertEquals($casts, $genre->getCasts());
    }

    public function testIfIncrementingIsFalse()
    {
        $genre = new Genre();
        $this->assertFalse($genre->incrementing);
    }

    public function testDatesAttibutes()
    {
        $dates = [
            'deleted_at','created_at','updated_at'
        ];
        $genre = new Genre();
        $this->assertEqualsCanonicalizing($dates, $genre->getDates());
    }
}
