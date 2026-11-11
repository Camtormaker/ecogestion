<x-app-layout>
    <div class="ecogestion-form-card mt-8">
        <div class="ecogestion-section">
            <span class="text-xs font-semibold uppercase tracking-widest text-emerald-100">EcoGestión</span>
            <h2 class="text-xl font-bold">Editar punto: {{ $point->name }}</h2>
        </div>
        <div class="p-6">

        <form action="{{ route('points.update', $point->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre del Punto</label>
                <input type="text" name="name" value="{{ old('name', $point->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('name') border-red-500 @enderror">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de Punto</label>
                <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="comunidad" {{ old('type', $point->type) == 'comunidad' ? 'selected' : '' }}>Comunidad</option>
                    <option value="educativo" {{ old('type', $point->type) == 'educativo' ? 'selected' : '' }}>Educativo</option>
                    <option value="empresa" {{ old('type', $point->type) == 'empresa' ? 'selected' : '' }}>Empresa</option>
                </select>
                @error('type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Dirección</label>
                <textarea name="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('address') border-red-500 @enderror">{{ old('address', $point->address) }}</textarea>
                @error('address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Persona de Contacto</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $point->contact_person) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                @error('contact_person') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Teléfono de Contacto</label>
                <input type="text" name="phone" value="{{ old('phone', $point->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="1" {{ old('status', $point->status) == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('status', $point->status) == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <a href="{{ route('points.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
        </div>
    </div>
</x-app-layout>
