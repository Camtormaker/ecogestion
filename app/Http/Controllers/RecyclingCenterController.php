<?php

namespace App\Http\Controllers;

use App\Models\RecyclingCenter;
use Illuminate\Http\Request;

class RecyclingCenterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $centers = RecyclingCenter::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
        })->paginate(10);

        return view('centers.index', compact('centers', 'search'));
    }

    public function create()
    {
        return view('centers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'required|string',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'status' => 'required|integer|in:0,1',
        ]);

        RecyclingCenter::create($validated);

        return redirect()->route('centers.index')->with('message', 'Centro de reciclaje registrado con éxito.');
    }

    public function edit(RecyclingCenter $center)
    {
        return view('centers.edit', compact('center'));
    }

    public function update(Request $request, RecyclingCenter $center)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'required|string',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'status' => 'required|integer|in:0,1',
        ]);

        $center->update($validated);

        return redirect()->route('centers.index')->with('message', 'Centro de reciclaje actualizado con éxito.');
    }

    public function destroy(RecyclingCenter $center)
    {
        $center->delete();
        return redirect()->route('centers.index')->with('message', 'Centro de reciclaje eliminado correctamente.');
    }
}
