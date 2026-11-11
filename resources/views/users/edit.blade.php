<x-app-layout>
    <div class="ecogestion-form-card mt-8">
        <div class="ecogestion-section">
            <span class="text-xs font-semibold uppercase tracking-widest text-emerald-100">EcoGestión</span>
            <h2 class="text-xl font-bold">Editar usuario: {{ $user->name }}</h2>
        </div>
        <div class="p-6">

        <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('name') border-red-500 @enderror">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('email') border-red-500 @enderror">
                @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Asignar Rol</label>
                <select name="role_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('role_id') border-red-500 @enderror">
                    <option value="">Selecciona un Rol...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contraseña (Dejar en blanco para mantener la actual)</label>
                <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('password') border-red-500 @enderror">
                @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Estado de la cuenta</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="1" {{ old('status', $user->status) == '1' ? 'selected' : '' }}>Activo / Permitir acceso</option>
                    <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>Inactivo / Bloquear acceso</option>
                </select>
                @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
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
