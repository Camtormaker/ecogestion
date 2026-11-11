<?php

namespace App\Http\Controllers;

use App\Models\CollectionPoint;
use Illuminate\Http\Request;

class CollectionPointController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $points = CollectionPoint::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%");
        })->paginate(10);

        return view('points.index', compact('points', 'search'));
    }

    public function create()
    {
        return view('points.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:educativo,comunidad,empresa',
            'address' => 'required|string',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|integer|in:0,1',
        ]);

        CollectionPoint::create($validated);

        return redirect()->route('points.index')->with('message', 'Punto de recolección registrado con éxito.');
    }

    public function edit(CollectionPoint $point)
    {
        return view('points.edit', compact('point'));
    }

    public function update(Request $request, CollectionPoint $point)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:educativo,comunidad,empresa',
            'address' => 'required|string',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|integer|in:0,1',
        ]);

        $point->update($validated);

        return redirect()->route('points.index')->with('message', 'Punto de recolección actualizado con éxito.');
    }

    public function destroy(CollectionPoint $point)
    {
        $point->delete();
        return redirect()->route('points.index')->with('message', 'Punto de recolección eliminado correctamente.');
    }
}
