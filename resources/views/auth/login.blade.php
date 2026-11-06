@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg" x-data="{ show: false }">
    <div class="text-center">
        <h1 class="text-2xl font-bold">Masuk</h1>
        <div class="mx-auto mt-3 h-1 w-12 rounded bg-brand-400"></div>
        <p class="mt-3 text-sm text-gray-500">Masuk menggunakan akun pegawai BPKAD Anda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="nama@email.com"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">Kata Sandi</label>
            <div class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                       placeholder="Masukkan kata sandi"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-16 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-brand-600 hover:text-brand-500"
                        x-text="show ? 'Sembunyikan' : 'Lihat'"></button>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 accent-amber-500">
            Ingat saya
        </label>

        <button type="submit"
                class="w-full rounded-xl bg-brand-400 py-3 font-semibold text-ink shadow-md transition hover:bg-brand-500">
            Login
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">Daftar</a>
    </p>
</div>
@endsection