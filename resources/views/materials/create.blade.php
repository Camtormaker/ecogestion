<x-app-layout>
    <div class="ecogestion-form-card mt-8">
        <div class="ecogestion-section">
            <span class="text-xs font-semibold uppercase tracking-widest text-emerald-100">EcoGestión</span>
            <h2 class="text-xl font-bold">Nuevo material reciclable</h2>
        </div>
        <div class="p-6">

        <form action="{{ route('materials.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre del Material</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('name') border-red-500 @enderror">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                <select name="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="plastico" {{ old('category') == 'plastico' ? 'selected' : '' }}>Plástico</option>
                    <option value="papel" {{ old('category') == 'papel' ? 'selected' : '' }}>Papel</option>
                    <option value="carton" {{ old('category') == 'carton' ? 'selected' : '' }}>Cartón</option>
                    <option value="vidrio" {{ old('category') == 'vidrio' ? 'selected' : '' }}>Vidrio</option>
                    <option value="aluminio" {{ old('category') == 'aluminio' ? 'selected' : '' }}>Aluminio</option>
                </select>
                @error('category') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                <input type="text" name="unit" value="{{ old('unit', 'kg') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                @error('unit') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <a href="{{ route('materials.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Registrar Material
                </button>
            </div>
        </form>
        </div>
    </div>
</x-app-layout>
