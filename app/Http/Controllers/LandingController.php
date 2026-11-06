<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'q' => 'Apa fungsi Sistem Persuratan BPKAD Kabupaten Madiun?',
                'a' => 'Sistem ini menggantikan surat fisik untuk komunikasi antarbagian di lingkungan BPKAD. Surat dibuat, dikirim, dan diterima secara digital sehingga lebih cepat, tertib, dan tercatat dengan baik.',
            ],
            [
                'q' => 'Bagaimana cara mendaftar dan apa fungsi pilihan bagian?',
                'a' => 'Klik tombol "Daftar", lengkapi data diri, lalu pilih bagian Anda: Sekretariat, Anggaran, Perbendaharaan, atau Aset & Akuntansi. Bagian yang dipilih menentukan template surat yang tersedia untuk Anda.',
            ],
            [
                'q' => 'Apakah sistem ini dapat digunakan oleh masyarakat umum?',
                'a' => 'Tidak. Sistem ini hanya untuk komunikasi internal pegawai BPKAD Kabupaten Madiun. Gunakan data diri yang sebenarnya dan jangan membagikan kata sandi kepada siapa pun.',
            ],
        ];

        return view('landing', compact('faqs'));
    }
}