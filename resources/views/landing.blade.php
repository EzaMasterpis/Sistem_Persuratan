<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Persuratan BPKAD Kabupaten Madiun</title>
    <meta name="description" content="Sistem persuratan digital BPKAD Kabupaten Madiun.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans text-ink antialiased">

    {{-- NAVBAR --}}
    <header class="bg-brand-400 shadow-md">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white">
                        <img src="{{ asset('images/logo-madiun.png') }}" alt="Logo Pemerintah Kabupaten Madiun"
                            class="h-9 w-auto" onerror="this.style.display='none'">
                    </span>
                <div class="leading-tight">
                    <p class="text-xs font-medium text-ink/80">Pemerintah Kabupaten Madiun</p>
                    <p class="text-sm font-bold text-ink">BPKAD</p>
                </div>
            </div>
            <nav class="hidden items-center gap-6 text-sm font-semibold text-ink sm:flex">
                <a href="#beranda" class="transition hover:text-white">Beranda</a>
                <a href="#faq" class="transition hover:text-white">FAQ</a>
            </nav>
        </div>
    </header>

    {{-- HERO --}}
    <section id="beranda" class="relative overflow-hidden bg-white px-4 py-16 sm:py-24">
        {{-- hiasan kuning tipis --}}
        <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-100/70 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-brand-100/70 blur-2xl"></div>

        <div class="relative mx-auto max-w-3xl text-center">

            <div class="mx-auto mb-5 flex h-24 w-24 items-center justify-center">
                <img src="{{ asset('images/logo-madiun.png') }}" alt="Logo Kabupaten Madiun"
                     class="max-h-24 w-auto"
                     onerror="this.outerHTML='<div class=&quot;flex h-24 w-24 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-600&quot;>Logo</div>'">
            </div>

            <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">
                Pemerintah Kabupaten Madiun
            </p>
            <h1 class="mt-2 text-3xl font-bold leading-tight sm:text-4xl">
                Sistem Persuratan <br class="hidden sm:block"> BPKAD Kabupaten Madiun
            </h1>
            <div class="mx-auto mt-4 h-1 w-16 rounded bg-brand-400"></div>

            <p class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-gray-600">
                Platform digital untuk komunikasi surat-menyurat antarbagian di lingkungan
                BPKAD Kabupaten Madiun. Menggantikan surat fisik dengan proses yang lebih
                cepat, tertib, dan terdokumentasi.
            </p>

            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('login') }}"
                   class="w-full rounded-xl bg-brand-400 px-8 py-3 text-center font-semibold text-ink shadow-md transition hover:bg-brand-500 sm:w-auto">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="w-full rounded-xl border-2 border-gray-300 bg-white px-8 py-3 text-center font-semibold text-ink transition hover:border-brand-400 hover:bg-brand-50 sm:w-auto">
                    Daftar
                </a>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="border-t border-gray-100 bg-white px-4 py-16">
        <div class="mx-auto max-w-3xl">
            <div class="text-center">
                <h2 class="text-2xl font-bold sm:text-3xl">Pertanyaan yang Sering Diajukan</h2>
                <div class="mx-auto mt-3 h-1 w-16 rounded bg-brand-400"></div>
            </div>

            <div class="mt-10 space-y-3" x-data="{ open: null }">
                @foreach ($faqs as $i => $faq)
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                         :class="open === {{ $i }} ? 'border-l-4 border-l-brand-400' : ''">
                        <button type="button"
                                @click="open = (open === {{ $i }}) ? null : {{ $i }}"
                                :aria-expanded="open === {{ $i }}"
                                class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-medium hover:bg-gray-50">
                            <span>{{ $faq['q'] }}</span>
                            <svg class="h-5 w-5 shrink-0 text-brand-500 transition-transform duration-200"
                                 :class="open === {{ $i }} ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-cloak x-transition.opacity
                             class="border-t border-gray-100 px-5 py-4 text-sm leading-relaxed text-gray-600">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-brand-400">
        <p class="px-4 py-6 text-center text-sm font-medium text-ink">
            &copy; {{ date('Y') }} BPKAD Kabupaten Madiun. Seluruh hak dilindungi.
        </p>
    </footer>

</body>
</html>