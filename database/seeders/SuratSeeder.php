<?php

namespace Database\Seeders;

use App\Models\Surat;
use App\Models\User;
use App\Notifications\SuratMasukNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuratSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $id = User::pluck('id', 'email');

        Surat::query()->delete();
        DB::table('notifications')->delete();

        $data = [
            ['dari' => 'sekretariat@bpkad.test', 'ke' => ['anggaran', 'perbendaharaan', 'aset_akuntansi'],
             'sifat' => 'segera', 'dibaca' => ['perbendaharaan'],
             'perihal' => 'Undangan Rapat Koordinasi Penyusunan RKA',
             'isi' => "Dimohon kehadirannya pada rapat koordinasi penyusunan RKA.\n\nHari/Tanggal: Senin\nPukul: 09.00 WIB\nTempat: Ruang Rapat Utama",
             'waktu' => now()->subHours(3)],

            ['dari' => 'sekretariat@bpkad.test', 'ke' => ['aset_akuntansi'], 'sifat' => 'penting',
             'perihal' => 'Permintaan Data Barang Milik Daerah',
             'isi' => 'Mohon dikirimkan rekapitulasi data BMD terbaru untuk keperluan laporan semester.',
             'waktu' => now()->subDays(2)],

            ['dari' => 'sekretariat@bpkad.test', 'ke' => ['anggaran', 'perbendaharaan'], 'sifat' => null,
             'dibaca' => ['anggaran', 'perbendaharaan'],
             'perihal' => 'Pemberitahuan Jadwal Apel Pagi',
             'isi' => 'Apel pagi dilaksanakan setiap hari Senin pukul 07.30 di halaman kantor.',
             'waktu' => now()->subDays(5)],

            ['dari' => 'anggaran@bpkad.test', 'ke' => ['sekretariat'], 'sifat' => 'penting',
             'perihal' => 'Usulan Perubahan Jadwal Rapat Anggaran',
             'isi' => 'Kami mengusulkan jadwal rapat anggaran dimajukan satu hari dari rencana semula.',
             'waktu' => now()->subDay()],

            ['dari' => 'anggaran@bpkad.test', 'ke' => ['sekretariat', 'perbendaharaan'], 'sifat' => 'rahasia',
             'dibaca' => ['sekretariat', 'perbendaharaan'],
             'perihal' => 'Usulan Mutasi Pegawai',
             'isi' => 'Terlampir usulan mutasi pegawai di lingkungan bagian Anggaran untuk dipertimbangkan.',
             'waktu' => now()->subDays(3)],

            ['dari' => 'perbendaharaan@bpkad.test', 'ke' => ['sekretariat'], 'sifat' => null,
             'dibaca' => ['sekretariat'],
             'perihal' => 'Penyampaian SPJ Bulan September',
             'isi' => 'Disampaikan dokumen SPJ bulan September untuk diverifikasi dan ditindaklanjuti.',
             'waktu' => now()->subDays(6)],

            ['dari' => 'aset@bpkad.test', 'ke' => ['sekretariat'], 'sifat' => null,
             'dibaca' => ['sekretariat'], 'arsip' => ['sekretariat'],
             'perihal' => 'Laporan Inventarisasi Aset Tahun 2025',
             'isi' => 'Laporan inventarisasi aset tahun 2025 telah selesai disusun.',
             'waktu' => now()->subMonths(9)],

            ['dari' => 'sekretariat@bpkad.test', 'ke' => ['perbendaharaan'], 'sifat' => 'penting', 'status' => 'draft',
             'perihal' => 'Draft Surat Edaran Disiplin Pegawai',
             'isi' => 'Draft belum selesai.',
             'waktu' => now()->subDay()],
        ];

        foreach ($data as $d) {
            $status = $d['status'] ?? 'terkirim';
            $waktu  = $d['waktu'];
            $dibaca = $d['dibaca'] ?? [];
            $arsip  = $d['arsip'] ?? [];

            $surat = Surat::forceCreate([
                'pengirim_id'       => $id[$d['dari']],
                'perihal'           => $d['perihal'],
                'isi'               => $d['isi'],
                'sifat'             => $d['sifat'],
                'status'            => $status,
                'dikirim_at'        => $status === 'terkirim' ? $waktu : null,
                'surat_dari'        => $d['surat_dari'] ?? null,
                'nomor_surat'       => $d['nomor_surat'] ?? null,
                'tanggal_surat'     => $d['tanggal_surat'] ?? null,
                'instruksi'         => $d['instruksi'] ?? null, // array langsung, jangan di-json_encode
                'isi_disposisi'     => $d['isi_disposisi'] ?? null,
                'pemberi_disposisi' => $d['pemberi'] ?? null,
                'keterangan'        => $d['keterangan'] ?? null,
                'created_at'        => $waktu,
                'updated_at'        => $waktu,
            ]);

            foreach ($d['ke'] as $bagian) {
                $surat->penerima()->create([
                    'bagian'        => $bagian,
                    'dibaca_at'     => in_array($bagian, $dibaca) ? $waktu->copy()->addHour() : null,
                    'diarsipkan_at' => in_array($bagian, $arsip) ? $waktu->copy()->addDay() : null,
                ]);
            }

            // Notifikasi untuk bagian yang belum membaca surat
            if ($status === 'terkirim') {
                $belum = array_diff($d['ke'], $dibaca, $arsip);

                User::whereIn('bagian', $belum)->get()
                    ->each(fn ($u) => $u->notify(new SuratMasukNotification($surat)));
            }
        }

        // Dipanggil sekali, setelah semua surat dibuat
        $this->beriNomorAgenda();
    }

    /** Nomor agenda: urut per tahun berdasarkan waktu kirim. Aman dijalankan berulang. */
    private function beriNomorAgenda(): void
    {
        // Kosongkan dulu agar tidak bentrok dengan nomor lama
        DB::table('surat')->update(['tahun_agenda' => null, 'nomor_agenda' => null]);

        $urut = [];

        Surat::terkirim()->orderBy('dikirim_at')->orderBy('id')->get()->each(function ($s) use (&$urut) {
            $tahun = $s->dikirim_at->year;
            $urut[$tahun] = ($urut[$tahun] ?? 0) + 1;

            DB::table('surat')->where('id', $s->id)->update([
                'tahun_agenda' => $tahun,
                'nomor_agenda' => $urut[$tahun],
            ]);
        });
    }
}