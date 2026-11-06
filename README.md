# Sistem Persuratan BPKAD Kabupaten Madiun

Aplikasi web untuk surat-menyurat internal di lingkungan **Badan Pengelolaan Keuangan dan Aset Daerah (BPKAD) Kabupaten Madiun**. Sistem ini menggantikan surat fisik antarbagian dengan proses digital yang lebih cepat, tertib, dan terdokumentasi, termasuk lembar disposisi seperti yang dipakai di instansi.

Tampilannya sederhana dengan dominasi warna putih dan kuning, sesuai nuansa situs pemerintahan.

## Fitur

**Akun dan bagian**
- Landing page dengan pengenalan sistem dan FAQ (dropdown).
- Daftar, login, dan logout. Saat mendaftar, pengguna memilih bagiannya: Sekretariat, Anggaran, Aset & Akuntansi, atau Perbendaharaan.
- Dua peran: **admin** (Sekretariat, wewenang lebih) dan **staf**.

**Surat**
- Antarmuka ala Gmail: tombol **Tulis Surat** membuka jendela di pojok kanan bawah, lengkap dengan sidebar folder.
- Folder: Kotak Masuk, Terkirim, Draft, Arsip, dan Disimpan.
- Kirim ke **beberapa bagian sekaligus** atau semua bagian selain pengirim. Bagian yang salah pilih bisa dihapus dari daftar.
- **Tembusan** (opsional) untuk bagian yang hanya perlu mengetahui.
- Sifat surat: Sangat Segera, Segera, Rahasia, Penting, atau tanpa sifat.
- **Lampiran** file (maksimal 5 file, 5 MB per file; PDF, Word, Excel, JPG, PNG). File disimpan privat dan hanya bisa dibuka oleh pengirim dan penerima surat.
- Draft yang bisa diedit sebelum dikirim.
- Filter berdasarkan sifat surat dan rentang tanggal, ditambah pencarian perihal dan isi surat.

**Lembar disposisi**
- Surat tampil di sisi penerima sebagai lembar disposisi: kop surat, surat dari, nomor dan tanggal surat, nomor agenda, sifat, perihal, diteruskan kepada, instruksi ("Dengan hormat harap"), isi disposisi, keterangan, dan diteruskan ke.
- Nomor agenda terisi otomatis saat surat dikirim, satu urutan untuk seluruh BPKAD dan dimulai dari 1 setiap tahun.
- Bagian disposisi diisi oleh admin (Sekretariat).

**Notifikasi dan status**
- Ikon lonceng di header dengan tiga jenis notifikasi: surat berhasil terkirim (untuk pengirim), surat baru atau tembusan (untuk bagian penerima), dan surat diterima (untuk pengirim).
- Status **dibaca** tercatat otomatis per bagian.
- Status **diterima** adalah konfirmasi manual lewat tombol **Terima Surat**, juga per bagian. Pengirim melihat status tiap bagian di halaman surat.
- **Simpan (bookmark)** pribadi untuk surat penting, muncul di folder Disimpan.
- Dialog konfirmasi dan toast memakai SweetAlert2.

## Teknologi

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 13 |
| Bahasa | PHP (dikembangkan dengan PHP 8.5) |
| Basis data | MySQL |
| Tampilan | Blade, Tailwind CSS v4 |
| Interaksi | Alpine.js, SweetAlert2 |
| Build aset | Vite |
| Lingkungan pengembangan | Laragon |

## Persyaratan

- PHP versi yang didukung Laravel 13, dengan ekstensi umum Laravel (mbstring, openssl, pdo_mysql, fileinfo, dan sebagainya)
- Composer
- Node.js dan npm
- MySQL
- [Laragon](https://laragon.org/) (disarankan di Windows; sudah memuat semua di atas)

## Instalasi

1. **Ambil proyek** dan masuk ke foldernya (di Laragon biasanya di `C:\laragon\www`):

   ```bash
   git clone <url-repositori> persuratan-bpkad
   cd persuratan-bpkad
   ```

2. **Pasang dependensi:**

   ```bash
   composer install
   npm install
   ```

3. **Siapkan file lingkungan:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Atur `.env`:**

   ```env
   APP_NAME="Sistem Persuratan BPKAD Kabupaten Madiun"
   APP_URL=http://127.0.0.1:8000
   APP_LOCALE=id
   APP_TIMEZONE=Asia/Jakarta

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=persuratan_bpkad
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Buat database** `persuratan_bpkad` di MySQL (lewat HeidiSQL di Laragon: klik kanan > Create new > Database).

6. **Jalankan migrasi dan seeder:**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

   Seeder membuat empat akun contoh (lihat bagian berikutnya) dan beberapa surat contoh.

7. **Letakkan logo** Pemerintah Kabupaten Madiun di `public/images/logo-madiun.png`. File logo tidak disertakan di repositori. Jika belum ada, halaman tetap tampil dengan kotak pengganti.

8. **Jalankan aplikasi.** Buka dua terminal:

   ```bash
   php artisan serve
   ```

   ```bash
   npm run dev
   ```

   Lalu buka `http://127.0.0.1:8000`.

   Alternatifnya, `npm run build` sekali untuk membuat aset produksi (diulang setiap kali mengubah CSS, JavaScript, atau kelas Tailwind), lalu cukup menjalankan `php artisan serve`.

## Akun contoh

Dibuat oleh `UserSeeder`. Semuanya memakai kata sandi **`password123`**.

| Bagian | Email | Peran |
|---|---|---|
| Sekretariat | `sekretariat@bpkad.test` | Admin |
| Anggaran | `anggaran@bpkad.test` | Staf |
| Perbendaharaan | `perbendaharaan@bpkad.test` | Staf |
| Aset & Akuntansi | `aset@bpkad.test` | Staf |

> Akun ini hanya untuk pengembangan. Ganti atau hapus sebelum sistem dipakai pegawai sebenarnya.

## Hak akses

| Tindakan | Siapa |
|---|---|
| Membaca surat terkirim | Pengirim dan pegawai di bagian penerima (utama maupun tembusan) |
| Mengedit atau menghapus draft | Hanya pengirimnya |
| Menghapus surat terkirim | Hanya admin (Sekretariat); surat dan lampirannya hilang untuk semua penerima |
| Mengarsipkan surat | Bagian penerima (per bagian) |
| Menerima surat | Pegawai di bagian penerima, bukan pengirim; berlaku per bagian |
| Mengisi bagian disposisi di form | Hanya admin (Sekretariat) |
| Menyimpan (bookmark) surat | Siapa pun yang berhak membaca surat; bersifat pribadi |

Aturan ini ada di `app/Policies/SuratPolicy.php`.

Akun yang dibuat lewat halaman daftar berperan **staf**. Untuk menjadikan akun sebagai admin, ubah kolom `role` menjadi `admin`, misalnya lewat `php artisan tinker`:

```php
App\Models\User::where('email', 'email@contoh.test')->update(['role' => 'admin']);
```

## Konfigurasi

Pengaturan khusus aplikasi ada di `config/bpkad.php`:

- `bagian`: daftar bagian dan urutannya.
- `jabatan`: nama jabatan di bagian "Diteruskan kepada Yth." pada lembar disposisi.
- `sifat`: pilihan sifat surat.
- `instruksi`: pilihan "Dengan hormat harap".
- `kop`: teks kop surat (nama instansi, alamat, telepon, email).
- `folders`: daftar folder di sidebar.
- `lampiran`: batas jumlah, ukuran, dan format file lampiran.

Setelah mengubah file ini, jalankan `php artisan config:clear`.

## Struktur penting

```
app/
├── Http/Controllers/
│   ├── AuthController.php        # daftar, login, logout
│   ├── LandingController.php     # landing page dan FAQ
│   ├── SuratController.php       # daftar, kirim, baca, edit, arsip, terima, simpan
│   ├── LampiranController.php    # unduh lampiran (dengan pemeriksaan hak akses)
│   └── NotifikasiController.php  # buka dan tandai notifikasi
├── Models/                       # User, Surat, SuratPenerima, Lampiran
├── Notifications/                # SuratMasuk, SuratTerkirim, SuratDiterima
└── Policies/SuratPolicy.php      # aturan hak akses surat
config/bpkad.php                  # pengaturan khusus aplikasi
database/seeders/                 # UserSeeder, SuratSeeder
resources/
├── css/app.css                   # Tailwind dan warna tema
├── js/app.js                     # Alpine.js dan SweetAlert2
└── views/
    ├── landing.blade.php
    ├── auth/                     # login dan daftar
    ├── layouts/                  # guest dan app (dashboard)
    ├── partials/                 # jendela Surat Baru, lembar disposisi, detail disposisi
    ├── components/               # ikon, badge sifat, pemilih penerima dan lampiran, kotak centang
    └── surat/                    # baca dan edit draft
routes/web.php
```

## Catatan keamanan

- **Lampiran** disimpan di `storage/app/private/lampiran/{id_surat}` dan tidak bisa diakses lewat URL langsung. Pengunduhan melalui `LampiranController`, yang memeriksa hak akses pada surat induknya.
- Pendaftaran saat ini **terbuka** bagi siapa pun yang bisa mengakses aplikasi. Untuk pemakaian sungguhan, batasi akses jaringan atau tambahkan persetujuan admin atas pendaftar baru.
- Sebelum produksi: set `APP_ENV=production`, `APP_DEBUG=false`, ganti kata sandi akun contoh, dan pertimbangkan pemindaian virus pada file yang diunggah.

## Pemecahan masalah

| Masalah | Solusi |
|---|---|
| `Vite manifest not found` atau tampilan tanpa gaya | Jalankan `npm run dev` atau `npm run build` |
| Kelas Tailwind baru tidak berpengaruh | Pastikan `npm run dev` berjalan, atau jalankan `npm run build` ulang, lalu Ctrl+F5 |
| Perubahan konfigurasi atau view tidak terbaca | `php artisan optimize:clear` |
| Gagal menjalankan migrasi | Pastikan MySQL di Laragon sudah Start dan database `persuratan_bpkad` sudah dibuat |
| Unggah lampiran gagal untuk file kecil | Naikkan `upload_max_filesize` dan `post_max_size` di `php.ini`, lalu restart server |
| Seeder surat gagal karena akun tidak ditemukan | Jalankan `php artisan db:seed` (memanggil `UserSeeder` lebih dulu) |

## Rencana pengembangan

- Cetak lembar disposisi ke A4.
- Balas surat dan riwayat percakapan (thread).
- Panel admin: persetujuan pendaftar, pengaturan peran, dan penonaktifan akun.
- Fitur lupa kata sandi.
- Template surat per bagian dan penomoran surat otomatis.
- Penerusan disposisi ke pegawai tertentu di dalam bidang.
- Paraf atau tanda tangan digital.
- Pengingat otomatis untuk surat yang belum diterima.
- Notifikasi real-time.

## Lisensi

Dikembangkan untuk keperluan internal BPKAD Kabupaten Madiun.
