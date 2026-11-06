<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Sistem Persuratan BPKAD Kabupaten Madiun</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php $aktifFolder = $folder ?? request('folder', 'masuk'); @endphp
</head>
<body class="h-screen overflow-hidden bg-white font-sans text-ink antialiased"
      x-data="{ sidebar: false, compose: {{ old('_form') === 'compose' ? 'true' : 'false' }}, minimized: false, profile: false, notif: false }"
      @keydown.escape.window="sidebar = false; profile = false; notif = false">

<div class="flex h-full flex-col">

    {{-- HEADER --}}
    <header class="relative z-40 flex shrink-0 items-center gap-2 bg-brand-400 px-3 py-2.5 shadow-md sm:gap-3 sm:px-4">
        <button type="button" @click="sidebar = true" aria-label="Buka menu"
                class="rounded-full p-2 text-ink transition hover:bg-white/40 lg:hidden">
            <x-icon name="bars" size="h-6 w-6" />
        </button>

        <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-2.5">
            <img src="{{ asset('images/logo-madiun.png') }}" alt="Logo Pemerintah Kabupaten Madiun"
                 class="h-9 w-auto" onerror="this.style.display='none'">
            <div class="hidden leading-tight sm:block">
                <p class="text-[11px] font-medium text-ink/80">Pemerintah Kabupaten Madiun</p>
                <p class="text-sm font-bold text-ink">Sistem Persuratan BPKAD</p>
            </div>
        </a>

        {{-- Pencarian --}}
        <form method="GET" action="{{ route('dashboard') }}" class="relative mx-1 min-w-0 flex-1 md:mx-6 md:max-w-2xl">
            <input type="hidden" name="folder" value="{{ $aktifFolder }}">
            @foreach (['sifat', 'dari', 'sampai'] as $k)
                @if (request()->filled($k))
                    <input type="hidden" name="{{ $k }}" value="{{ request($k) }}">
                @endif
            @endforeach

            <x-icon name="search" size="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari perihal atau isi surat..."
                   class="w-full rounded-full border-0 bg-white/90 py-2 pl-11 pr-4 text-sm outline-none ring-1 ring-transparent transition placeholder:text-gray-400 focus:bg-white focus:ring-brand-600">
        </form>

        <div class="ml-auto flex items-center gap-1 sm:gap-2">

            {{-- Notifikasi --}}
            <div class="relative">
                <button type="button" @click="notif = !notif; profile = false" aria-label="Notifikasi"
                    class="relative rounded-full p-2 text-white transition hover:bg-white/30">
                    <x-icon name="bell" size="h-6 w-6 drop-shadow-sm" />
                    @if ($notifUnread > 0)
                        <span class="absolute right-0 top-0 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">
                            {{ $notifUnread > 9 ? '9+' : $notifUnread }}
                        </span>
                    @endif
                </button>

                <div x-show="notif" x-cloak x-transition @click.outside="notif = false"
                     class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-1.5rem)] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">

                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                        <p class="text-sm font-semibold">Notifikasi</p>
                        @if ($notifUnread > 0)
                            <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-brand-600 hover:underline">
                                    Tandai semua dibaca
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                         @forelse ($notifLatest as $n)
                            @php
                                $tipe  = $n->data['tipe'] ?? 'masuk';
                                $ikon  = ['masuk' => 'inbox', 'terkirim' => 'send', 'diterima' => 'check'][$tipe] ?? 'inbox';
                                $warna = ['masuk' => 'bg-brand-100 text-brand-600', 'diterima' => 'bg-green-50 text-green-600'][$tipe] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <a href="{{ route('notifikasi.buka', $n->id) }}"
                               class="flex gap-3 border-b border-gray-100 px-4 py-3 text-sm transition hover:bg-gray-50 {{ $n->read_at ? '' : 'bg-brand-50' }}">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $warna }}">
                                    <x-icon :name="$ikon" size="h-4 w-4" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block {{ $n->read_at ? 'font-medium' : 'font-semibold' }}">{{ $n->data['judul'] ?? 'Notifikasi' }}</span>
                                    <span class="block truncate text-xs text-gray-500">{{ $n->data['pesan'] ?? '' }}</span>
                                    <span class="mt-0.5 block text-[11px] text-gray-400">{{ $n->created_at->diffForHumans() }}</span>
                                </span>
                                @unless ($n->read_at)
                                    <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>
                                @endunless
                            </a>
                        @empty
                            <p class="px-4 py-10 text-center text-sm text-gray-400">Belum ada notifikasi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Profil --}}
            <div class="relative">
                <button type="button" @click="profile = !profile; notif = false" aria-label="Menu profil"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-sm font-bold text-brand-600 shadow ring-2 ring-white/60 transition hover:ring-white">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </button>

                <div x-show="profile" x-cloak x-transition @click.outside="profile = false"
                     class="absolute right-0 mt-2 w-64 rounded-xl border border-gray-200 bg-white p-4 shadow-xl">
                    <p class="truncate font-semibold">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    <div class="mt-3 space-y-1 border-t border-gray-100 pt-3 text-xs text-gray-600">
                        <p>Bagian: <span class="font-semibold">{{ auth()->user()->bagian_label }}</span></p>
                        <p>Role: <span class="font-semibold">{{ auth()->user()->isAdmin() ? 'Admin' : 'Staf' }}</span></p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-lg border border-gray-300 py-2 text-sm font-semibold transition hover:border-brand-400 hover:bg-brand-50">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="relative flex min-h-0 flex-1">

        {{-- Overlay sidebar (mobile) --}}
        <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-40 bg-black/30 lg:hidden"></div>

        {{-- SIDEBAR --}}
        <aside class="fixed inset-y-0 left-0 z-50 flex w-64 transform flex-col overflow-y-auto border-r border-gray-200 bg-white p-4 transition-transform duration-200 lg:static lg:z-auto lg:translate-x-0"
               :class="sidebar ? 'translate-x-0' : '-translate-x-full'">

            <button type="button" @click="compose = true; minimized = false; sidebar = false"
                    class="flex w-fit items-center gap-3 rounded-2xl bg-brand-400 px-6 py-3.5 font-semibold text-ink shadow-md transition hover:bg-brand-500 hover:shadow-lg">
                <x-icon name="plus" size="h-6 w-6" />
                Tulis Surat
            </button>

            <nav class="mt-6 space-y-1">
                @foreach ($folders as $key => $f)
                    @php $aktif = $aktifFolder === $key; @endphp
                    <a href="{{ route('dashboard', ['folder' => $key]) }}"
                       class="flex items-center gap-3 rounded-full px-4 py-2 text-sm transition {{ $aktif ? 'bg-brand-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <x-icon :name="$f['icon']" />
                        <span class="flex-1">{{ $f['label'] }}</span>
                        @if ($f['badge'] > 0)
                            <span class="text-xs font-semibold">{{ $f['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto pt-8">
                <div class="rounded-xl border border-brand-200 bg-brand-50 p-3 text-xs">
                    <p class="text-gray-500">Bagian Anda</p>
                    <p class="mt-0.5 text-sm font-semibold">{{ auth()->user()->bagian_label }}</p>
                </div>
            </div>
        </aside>

        {{-- KONTEN --}}
        <main class="min-w-0 flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

@php $pesanFlash = session('error') ?? session('status'); @endphp
@if ($pesanFlash)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.notifikasi(@js($pesanFlash), @js(session('error') ? 'error' : 'success'));
        });
    </script>
@endif

@include('partials.compose')

</body>
</html>