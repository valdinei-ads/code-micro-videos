<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        return Genre::all();
    }

    public function show(Genre $genre)
    {
        return $genre;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:150',
            'is_active' => 'boolean'
        ]);

        return Genre::create($request->all());
    }

    public function update(Request $request, Genre $genre)
    {
        $this->validate($request, [
            'name' => 'required|max:150',
            'is_active' => 'boolean'
        ]);

        $genre->update($request->all());

        return $genre;
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->noContent();
    }
}
