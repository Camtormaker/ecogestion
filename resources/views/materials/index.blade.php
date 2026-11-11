<x-app-layout>
    <div class="ecogestion-page ecogestion-card p-4 sm:p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-emerald-600">EcoGestión</span>
                <h2 class="ecogestion-title">Catálogo de materiales reciclables</h2>
                <p class="mt-1 text-sm text-zinc-500">Administra los residuos y materiales aceptados por el sistema.</p>
            </div>
            <a href="{{ route('materials.create') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                + Nuevo Material
            </a>
        </div>

        @if (session('message'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Buscador tradicional mediante formulario GET -->
        <form action="{{ route('materials.index') }}" method="GET" class="mb-4 flex gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar material..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">Buscar</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Categoría</th>
                        <th class="px-6 py-3">Unidad</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $material->name }}</td>
                            <td class="px-6 py-4 capitalize">{{ $material->category }}</td>
                            <td class="px-6 py-4">{{ $material->unit }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $material->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $material->status ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('materials.edit', $material) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">Editar</a>
                                
                                <!-- Eliminación tradicional mediante formulario POST/DELETE -->
                                <form action="{{ route('materials.destroy', $material) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este material?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">Eliminar</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-400">No se encontraron materiales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $materials->appends(['search' => $search])->links() }}
        </div>
    </div>
</x-app-layout>
