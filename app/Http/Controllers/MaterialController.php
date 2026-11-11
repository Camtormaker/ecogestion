<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $materials = Material::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
        })->paginate(10);

        return view('materials.index', compact('materials', 'search'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'category' => 'required|in:plastico,papel,carton,vidrio,aluminio',
            'unit' => 'required|string|max:20',
            'status' => 'required|integer|in:0,1',
        ]);

        Material::create($validated);

        return redirect()->route('materials.index')->with('message', 'Material registrado con éxito.');
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'category' => 'required|in:plastico,papel,carton,vidrio,aluminio',
            'unit' => 'required|string|max:20',
            'status' => 'required|integer|in:0,1',
        ]);

        $material->update($validated);

        return redirect()->route('materials.index')->with('message', 'Material actualizado con éxito.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('message', 'Material eliminado correctamente.');
    }
}
