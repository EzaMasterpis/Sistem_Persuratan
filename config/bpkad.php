<?php

return [
    // Urutan mengikuti lembar disposisi
    'bagian' => [
        'sekretariat'    => 'Sekretariat',
        'anggaran'       => 'Anggaran',
        'aset_akuntansi' => 'Aset & Akuntansi',
        'perbendaharaan' => 'Perbendaharaan',
    ],

    // Nama jabatan yang tercetak di bagian "Diteruskan kepada Yth."
    'jabatan' => [
        'sekretariat'    => 'Sekretaris',
        'anggaran'       => 'Kepala Bidang Anggaran',
        'aset_akuntansi' => 'Kepala Bidang Aset dan Akuntansi',
        'perbendaharaan' => 'Kepala Bidang Perbendaharaan',
    ],

    // Sifat surat (urutan mengikuti kotak di lembar). Tanpa sifat disimpan sebagai null.
    'sifat' => [
        'sangat_segera' => 'Sangat Segera',
        'segera'        => 'Segera',
        'rahasia'       => 'Rahasia',
        'penting'       => 'Penting',
    ],

    // Bagian "Dengan hormat harap"
    'instruksi' => [
        'tanggapan'  => 'Tanggapan dan saran',
        'proses'     => 'Proses Lebih Lanjut',
        'koordinasi' => 'Koordinasi / Konfirmasi',
    ],

    'kop' => [
        'instansi' => 'Pemerintah Kabupaten Madiun',
        'badan'    => 'Badan Pengelolaan Keuangan dan Aset Daerah',
        'alamat'   => 'Jl. MT. Haryono No.4 Telp. 0351 – 473022',
        'email'    => 'bpkadmdn@gmail.com',
        'kota'     => 'Caruban 63153',
    ],

    'folders' => [
        'masuk'    => ['label' => 'Kotak Masuk', 'icon' => 'inbox'],
        'terkirim' => ['label' => 'Terkirim',    'icon' => 'send'],
        'draft'    => ['label' => 'Draft',       'icon' => 'draft'],
        'arsip'    => ['label' => 'Arsip',       'icon' => 'archive'],
        'disimpan' => ['label' => 'Disimpan',    'icon' => 'star'],
    ],

    'lampiran' => [
        'maks_file' => 5,
        'maks_kb'   => 5120, // 5 MB
        'ekstensi'  => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'],
    ],
];