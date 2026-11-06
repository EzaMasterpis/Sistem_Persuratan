<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Data dummy: nanti diganti dengan data dari database
        $semua = collect([
            ['folder' => 'masuk', 'pihak' => 'Budi Santoso', 'bagian' => 'Sekretariat', 'perihal' => 'Undangan Rapat Koordinasi Penyusunan RKA', 'ringkasan' => 'Dimohon hadir pada rapat koordinasi yang akan dilaksanakan hari Senin pukul 09.00 di ruang rapat utama.', 'sifat' => 'Segera', 'lampiran' => true, 'dibaca' => false, 'tanggal' => '08:15'],
            ['folder' => 'masuk', 'pihak' => 'Siti Rahayu', 'bagian' => 'Anggaran', 'perihal' => 'Permohonan Data Realisasi Anggaran Triwulan III', 'ringkasan' => 'Mohon dikirimkan rekapitulasi realisasi belanja sampai dengan akhir September.', 'sifat' => 'Penting', 'lampiran' => false, 'dibaca' => false, 'tanggal' => 'Kemarin'],
            ['folder' => 'masuk', 'pihak' => 'Agus Prasetyo', 'bagian' => 'Perbendaharaan', 'perihal' => 'Penyampaian SPJ Bulan September', 'ringkasan' => 'Terlampir dokumen SPJ untuk diverifikasi dan ditindaklanjuti.', 'sifat' => 'Biasa', 'lampiran' => true, 'dibaca' => true, 'tanggal' => '15 Okt'],
            ['folder' => 'masuk', 'pihak' => 'Dewi Lestari', 'bagian' => 'Aset & Akuntansi', 'perihal' => 'Rekonsiliasi Barang Milik Daerah Semester I', 'ringkasan' => 'Mohon dijadwalkan rekonsiliasi data BMD bersama bagian terkait.', 'sifat' => 'Penting', 'lampiran' => true, 'dibaca' => true, 'tanggal' => '14 Okt'],
            ['folder' => 'masuk', 'pihak' => 'Budi Santoso', 'bagian' => 'Sekretariat', 'perihal' => 'Pemberitahuan Jadwal Apel Pagi', 'ringkasan' => 'Apel pagi dilaksanakan setiap hari Senin pukul 07.30 di halaman kantor.', 'sifat' => 'Biasa', 'lampiran' => false, 'dibaca' => true, 'tanggal' => '12 Okt'],
            ['folder' => 'masuk', 'pihak' => 'Siti Rahayu', 'bagian' => 'Anggaran', 'perihal' => 'Revisi Dokumen Pelaksanaan Anggaran', 'ringkasan' => 'Terdapat perubahan pada beberapa kode rekening, mohon dicek kembali.', 'sifat' => 'Biasa', 'lampiran' => false, 'dibaca' => true, 'tanggal' => '10 Okt'],

            ['folder' => 'terkirim', 'pihak' => 'Anggaran', 'bagian' => 'Anggaran', 'perihal' => 'Tanggapan Permohonan Data Realisasi', 'ringkasan' => 'Data realisasi telah kami siapkan dan terlampir pada surat ini.', 'sifat' => 'Biasa', 'lampiran' => true, 'dibaca' => true, 'tanggal' => '16 Okt'],
            ['folder' => 'terkirim', 'pihak' => 'Perbendaharaan', 'bagian' => 'Perbendaharaan', 'perihal' => 'Permintaan Tanda Tangan Dokumen SP2D', 'ringkasan' => 'Mohon dokumen terlampir dapat segera diproses.', 'sifat' => 'Penting', 'lampiran' => true, 'dibaca' => true, 'tanggal' => '13 Okt'],

            ['folder' => 'draft', 'pihak' => 'Aset & Akuntansi', 'bagian' => 'Aset & Akuntansi', 'perihal' => 'Usulan Penghapusan Barang Rusak Berat', 'ringkasan' => 'Draft belum selesai.', 'sifat' => 'Biasa', 'lampiran' => false, 'dibaca' => true, 'tanggal' => '11 Okt'],

            ['folder' => 'arsip', 'pihak' => 'Dewi Lestari', 'bagian' => 'Aset & Akuntansi', 'perihal' => 'Laporan Inventarisasi Tahun 2025', 'ringkasan' => 'Laporan inventarisasi aset telah selesai disusun.', 'sifat' => 'Biasa', 'lampiran' => true, 'dibaca' => true, 'tanggal' => '02 Jan'],
        ]);

        $folders = [
            'masuk'    => ['label' => 'Kotak Masuk', 'icon' => 'inbox',   'badge' => $semua->where('folder', 'masuk')->where('dibaca', false)->count()],
            'terkirim' => ['label' => 'Terkirim',    'icon' => 'send',    'badge' => 0],
            'draft'    => ['label' => 'Draft',       'icon' => 'draft',   'badge' => $semua->where('folder', 'draft')->count()],
            'arsip'    => ['label' => 'Arsip',       'icon' => 'archive', 'badge' => 0],
        ];

        $folder = $request->query('folder', 'masuk');
        if (! array_key_exists($folder, $folders)) {
            $folder = 'masuk';
        }

        $daftar = $semua->where('folder', $folder)->values();

        return view('dashboard', compact('folders', 'folder', 'daftar'));
    }
}