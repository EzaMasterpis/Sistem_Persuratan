<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Sistem Persuratan BPKAD Kabupaten Madiun</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-ink antialiased">

    {{-- HEADER --}}
    <header class="bg-brand-400 shadow-md">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-madiun.png') }}" alt="Logo Pemerintah Kabupaten Madiun"
                     class="h-10 w-auto" onerror="this.style.display='none'">
                <div class="leading-tight">
                    <p class="text-xs font-medium text-ink/80">Pemerintah Kabupaten Madiun</p>
                    <p class="text-sm font-bold text-ink">BPKAD</p>
                </div>
            </a>
            <a href="{{ route('landing') }}" class="text-sm font-semibold text-ink transition hover:text-white">
                &larr; Beranda
            </a>
        </div>
    </header>

    {{-- KONTEN --}}
    <main class="relative flex flex-1 items-center justify-center overflow-hidden px-4 py-12">
        <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-100/70 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-brand-100/70 blur-2xl"></div>

        <div class="relative w-full max-w-md">
            @yield('content')
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-brand-400">
        <p class="px-4 py-6 text-center text-sm font-medium text-ink">
            &copy; {{ date('Y') }} BPKAD Kabupaten Madiun. Seluruh hak dilindungi.
        </p>
    </footer>

</body>
</html>