@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
<div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg" x-data="{ show: false }">
    <div class="text-center">
        <h1 class="text-2xl font-bold">Daftar Akun</h1>
        <div class="mx-auto mt-3 h-1 w-12 rounded bg-brand-400"></div>
        <p class="mt-3 text-sm text-gray-500">Gunakan data pegawai BPKAD yang sebenarnya.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nip" class="mb-1 block text-sm font-medium">
                NIP <span class="font-normal text-gray-400">(opsional)</span>
            </label>
            <input id="nip" type="text" name="nip" value="{{ old('nip') }}" inputmode="numeric"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
            @error('nip')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="bagian" class="mb-1 block text-sm font-medium">Bagian</label>
            <select id="bagian" name="bagian" required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                <option value="" disabled {{ old('bagian') ? '' : 'selected' }}>Pilih bagian Anda</option>
                @foreach ($bagian as $key => $label)
                    <option value="{{ $key }}" {{ old('bagian') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-400">Bagian menentukan template surat yang tersedia untuk Anda.</p>
            @error('bagian')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">Kata Sandi</label>
            <div class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                       placeholder="Minimal 8 karakter"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-16 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-brand-600 hover:text-brand-500"
                        x-text="show ? 'Sembunyikan' : 'Lihat'"></button>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-brand-400 py-3 font-semibold text-ink shadow-md transition hover:bg-brand-500">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Login</a>
    </p>
</div>
@endsection