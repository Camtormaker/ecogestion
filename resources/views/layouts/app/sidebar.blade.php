<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-emerald-50/40 text-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-emerald-100 bg-white">
            
            <flux:sidebar.header>

                <a href="{{ route('dashboard') }}"
                    wire:navigate
                    class="flex items-center gap-3 px-2 py-2 w-full">

                    {{-- LOGO DE RECICLAJE --}}
                    <div class="flex items-center justify-center
                                size-14 rounded-xl bg-green-600
                                shrink-0 shadow.md">

                        <span class="text-5xl leading-none">♻️</span>

                    </div>

                    {{-- NOMBRE Y DESCRIPCIÓN --}}
                    <div class="flex flex-col min-w-0">

                        <span class="text-xl font-extrabold text-green-700 leading-tight">
                            EcoGestión
                        </span>

                        <span class="text-xs text-zinc-500
                                    leading-relaxed">
                            Sistema de gestión de residuos reciclables
                        </span>

                    </div>

                </a>

                <flux:sidebar.collapse class="lg:hidden" />

            </flux:sidebar.header>


            <flux:sidebar.nav>

                <flux:sidebar.group :heading="__('Catálogos')" class="grid">
                    <flux:sidebar.item icon="cube" :href="route('materials.index')" :current="request()->routeIs('materials.*')" wire:navigate>
                        {{ __('Materiales') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="map-pin" :href="route('points.index')" :current="request()->routeIs('points.*')" wire:navigate>
                        {{ __('Puntos de recolección') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="building-office" :href="route('centers.index')" :current="request()->routeIs('centers.*')" wire:navigate>
                        {{ __('Centros de reciclaje') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if (auth()->user()->isAdministrator())
                    <flux:sidebar.group :heading="__('Administración')" class="grid">
                        <flux:sidebar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                            {{ __('Usuarios') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
