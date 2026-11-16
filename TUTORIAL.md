# Tutorial Project 5 — MVC PHP (Kategori dan Produk)

Aplikasi ECOMMERCE sederhana dengan arsitektur MVC dan routing otomatis `controller/method/parameter`.
Bagian Kategori dilengkapi (hapus, validasi), bagian Produk diselesaikan (CRUD, detail, unggah foto),
dan ditambahkan modul **Penjualan** (transaksi dengan banyak produk, stok otomatis berkurang, nota cetak).

## 1. Persiapan
- XAMPP (PHP 8.1 atau lebih baru; di komputer Anda PHP 8.3) dengan **Apache** dan **MySQL** aktif
- Ekstensi `fileinfo` aktif (bawaan XAMPP; dipakai untuk memeriksa isi file foto)
- Modul `mod_rewrite` aktif dan `AllowOverride All` pada htdocs (bawaan XAMPP)

## 2. Pasang proyek
1. Ekstrak zip, pastikan foldernya bernama `project5`
2. Pindahkan ke `C:\xampp\htdocs\project5`
3. Isinya harus seperti ini:

```
project5/
├── app/
│   ├── controllers/  Index, Dashboard, Kategori, Produk, Penjualan
│   ├── core/         Bootstrap, Controller, Model, Error, Csrf, Upload
│   ├── models/       Kategori, Produk, Penjualan
│   └── views/        dashboard, index, kategori, produk, penjualan
├── inc/              config.php, helpers.php
├── layouts/          dashboard.php, guest.php, assets/
├── uploads/produk/   tempat foto produk
├── vendor/           Whoops dan autoload Composer
├── .htaccess  index.php  composer.json
├── database.sql            database lengkap (instalasi baru)
└── update-penjualan.sql    tambahan tabel penjualan (database lama)
```

Folder `vendor/` sudah ikut di zip. Kalau hilang, jalankan `composer install` di folder proyek.

## 3. Buat database
1. Buka `http://localhost/phpmyadmin`
2. Tab **Import** → pilih `database.sql` → **Import**
3. Terbentuk database `dbproject5` berisi `tb_kategori`, `tb_produk`, `tb_penjualan`, dan `tb_penjualan_detail`

**Jika database `dbproject5` sudah ada dari versi sebelumnya** (sudah punya kategori dan produk),
jangan import `database.sql`. Cukup import **`update-penjualan.sql`** (tab Import, sama seperti di atas).
Data kategori dan produk Anda tidak berubah, hanya dua tabel penjualan yang ditambahkan.

> Kalau database `dbproject5` sudah ada dari praktikum lain dan tabelnya berbeda,
> hapus database itu di phpMyAdmin (tab Operasi → Hapus database) lalu import `database.sql`.

## 4. Sesuaikan konfigurasi
Buka `inc/config.php`:

| Pengaturan | Isi bawaan | Kapan diubah |
|---|---|---|
| `URL` | `http://localhost/project5` | Jika memakai virtual host atau port lain (contoh dosen: `http://pbwl.tld:8080/project5`) |
| `DB_USER` | `root` | Jika user MySQL berbeda |
| `DB_PASS` | kosong | Jika MySQL Anda memakai password (contoh dosen: `root`) |
| `DEBUG` | `true` | Ubah ke `false` saat dipublikasikan, agar detail error tidak tampil |

## 5. Jalankan
Buka `http://localhost/project5/dashboard`

| Alamat | Fungsi |
|---|---|
| `/dashboard` | Ringkasan jumlah kategori dan produk |
| `/kategori` | Daftar kategori |
| `/kategori/input` | Form tambah kategori |
| `/kategori/edit/1` | Form ubah kategori id 1 |
| `/produk` | Daftar produk |
| `/produk/input` | Form tambah produk (dengan foto) |
| `/produk/detail/1` | Detail produk id 1 |
| `/produk/edit/1` | Form ubah produk id 1 |
| `/penjualan` | Daftar transaksi penjualan |
| `/penjualan/input` | Form penjualan baru (banyak produk) |
| `/penjualan/detail/1` | Nota penjualan id 1 (bisa dicetak) |

Aksi simpan, update, dan hapus hanya lewat tombol formulir (POST).

## 6. Daftar uji
**Kategori**
1. Tambah kategori baru → muncul pesan hijau "Kategori berhasil disimpan."
2. Tambah dengan nama kosong atau nama yang sudah ada → muncul pesan merah, isian tidak hilang
3. Edit nama → "Kategori berhasil diperbarui."
4. Hapus kategori yang **belum** punya produk → berhasil
5. Hapus kategori yang **sudah** punya produk → ditolak dengan pesan merah

**Produk**
1. Tambah produk tanpa foto → berhasil, kolom foto tampil `-`
2. Tambah produk dengan foto JPG atau PNG → foto tampil di daftar dan di detail
3. Coba unggah file `.txt` atau `.php` yang namanya diganti `.jpg` → ditolak ("Foto harus berupa gambar...")
4. Coba unggah gambar lebih dari 2 MB → ditolak
5. Kode produk ganda → "Kode produk sudah digunakan."
6. Harga berisi huruf atau negatif (lewat inspect element) → ditolak oleh server
7. Edit tanpa memilih foto → foto lama tetap; edit dengan foto baru → foto lama terhapus dari `uploads/produk/`
8. Hapus produk → data dan file fotonya hilang

**Penjualan**
1. Buka Penjualan → Input Penjualan, isi pembeli, pilih produk dan jumlah. Subtotal dan total tampil otomatis
2. Klik **+ Tambah Produk** untuk menambah baris, tombol × untuk menghapus baris
3. Simpan → pindah ke halaman nota dengan nomor `INV-tanggal-nomor`
4. Buka menu Produk → stok produk tadi **berkurang** sesuai jumlah yang dijual
5. Pilih produk yang sama di dua baris → jumlahnya digabung menjadi satu item
6. Jual melebihi stok → ditolak dengan pesan "Stok ... tidak mencukupi (tersisa N)" dan stok tidak berubah
7. Ubah harga produk di menu Produk → nota lama tetap memakai harga saat transaksi
8. Hapus penjualan → stok produk **kembali** seperti semula
9. Hapus produk yang sudah pernah terjual → ditolak, pesan merah
10. Klik **Cetak** di halaman nota → menu dan tombol tidak ikut tercetak
11. Dashboard menampilkan jumlah transaksi dan total penjualan

**Keamanan**
1. Buka `http://localhost/project5/produk/hapus` langsung di peramban → "Metode tidak diizinkan."
2. Buka `http://localhost/project5/kategori/dashboard` → 404 (method bawaan Controller tidak bisa dipanggil lewat URL)
3. Buka `http://localhost/project5/index/detail/<script>alert(1)</script>` → tampil sebagai teks, tidak dijalankan
4. Buka `http://localhost/project5/inc/config.php` atau `/app/core/Model.php` → 403
5. Buka `http://localhost/project5/database.sql` atau `/update-penjualan.sql` → 403

Ambil tangkapan layar tiap hasil uji untuk dokumentasi.

## 7. Cara kerja
Alur `http://localhost/project5/produk/edit/3`:
1. `.htaccess` mengubah alamat menjadi `index.php?page=produk/edit/3`
2. `index.php` memuat `inc/config.php` (session, konstanta, autoload, Whoops) lalu menjalankan `Bootstrap`
3. `Bootstrap` memecah URL: controller `Produk`, method `edit`, parameter `3`
4. Method hanya dijalankan jika `public`, dibuat di controller itu sendiri, dan jumlah parameternya cocok
5. `Produk::edit(3)` meminta data ke model `Produk`, lalu memanggil `dashboard('produk/edit', $data)`
6. `layouts/dashboard.php` menampilkan pesan, lalu memuat view `produk/edit.php`

Pembagian tugas: Model berisi query PDO, Controller berisi validasi dan alur, View hanya HTML.

## 8. Apa saja yang ditambahkan
| File | Perubahan |
|---|---|
| `database.sql` | Tabel `tb_produk` (sebelumnya tidak ada), relasi ke `tb_kategori`, nama kategori unik, tabel penjualan, data contoh |
| `models/Kategori.php` | `simpan`, `update`, `hapus` memakai parameter, bukan membaca `$_POST` langsung |
| `models/Produk.php` | Query asli untuk tampil, tambah, ubah, hapus, dan daftar kategori |
| `controllers/Kategori.php` | Validasi, aksi `update` dan `hapus` |
| `controllers/Produk.php` | `simpan`, `detail`, `edit`, `update`, `hapus` beserta validasi dan unggah foto |
| `controllers/Dashboard.php` | Menghitung jumlah kategori, produk, transaksi, dan total penjualan |
| `controllers/Penjualan.php` | Daftar, input, detail (nota), dan hapus penjualan beserta validasi |
| `models/Penjualan.php` | Penyimpanan penjualan dan pengurangan stok dalam satu transaksi database; hapus mengembalikan stok |
| `views/penjualan/*` | Daftar, form dengan baris produk dinamis, dan nota |
| `layouts/assets/js/penjualan.js` | Tambah atau hapus baris dan hitung subtotal di form |
| `update-penjualan.sql` | Tabel `tb_penjualan` dan `tb_penjualan_detail` untuk database yang sudah ada |
| `core/Bootstrap.php` | Pembatasan method yang bisa dipanggil lewat URL, nama controller divalidasi, URL dasar dihitung benar saat project ada di subfolder |
| `core/Controller.php` | `flash`, `redirect`, `wajibPost`, `ambilId`, method layout dijadikan `protected` |
| `core/Model.php` | Koneksi dari konstanta, mode error PDO, `utf8mb4` |
| `core/Csrf.php`, `core/Upload.php` | Token CSRF dan penyimpanan foto yang aman |
| `inc/config.php`, `inc/helpers.php` | Konfigurasi, fungsi `e()`, `rupiah()`, `csrf_field()` |
| `views/*` | Output di-escape dengan `e()`, formulir memakai token CSRF, view produk lengkap |
| `.htaccess`, `uploads/.htaccess` | Blokir akses ke file internal, folder unggahan tidak menjalankan PHP |

## 9. Keamanan yang diterapkan
- Semua query memakai *prepared statement* (aman dari SQL injection)
- Semua data di view lewat `e()` (aman dari XSS), termasuk view bawaan `index/show` dan `index/detail`
- Setiap formulir POST membawa token CSRF; cookie session `HttpOnly` dan `SameSite=Lax`
- Hapus dan ubah data hanya lewat POST
- Foto: tipe diperiksa dari isi file (bukan dari nama), maksimal 2 MB, nama diacak, folder tidak bisa menjalankan PHP
- Detail koneksi database tidak tampil saat `DEBUG` bernilai `false`
- Penjualan: stok diperiksa dan dikurangi di dalam transaksi database dengan penguncian baris (`FOR UPDATE`),
  sehingga dua transaksi bersamaan tidak bisa membuat stok minus; bila ada langkah gagal semuanya dibatalkan
- Harga, kode, dan nama produk dicatat pada setiap item penjualan, total selalu dihitung ulang di server
  (angka dari form hanya untuk tampilan), dan produk yang sudah terjual tidak bisa dihapus

## 10. Masalah umum
| Gejala | Penyebab dan solusi |
|---|---|
| Semua alamat 404 atau muncul halaman Apache | `mod_rewrite` mati, atau folder bukan bernama `project5`. Cek juga `URL` di `inc/config.php` |
| Halaman tampil tanpa gaya (polos) | `URL` di `inc/config.php` tidak sama dengan alamat yang Anda buka |
| `error! SQLSTATE[HY000] [1049] Unknown database` | `database.sql` belum diimport |
| `error! ... [1045] Access denied` | `DB_USER` atau `DB_PASS` tidak cocok dengan MySQL Anda |
| `Table 'dbproject5.tb_penjualan' doesn't exist` | `update-penjualan.sql` belum diimport |
| Tanggal transaksi bergeser sehari | Zona waktu diatur `Asia/Jakarta` di `inc/config.php`; ubah bila perlu |
| Foto gagal tersimpan | Cek folder `uploads/produk/` ada dan bisa ditulis; pastikan ekstensi `fileinfo` aktif di `php.ini` |
| "Token formulir tidak valid" | Halaman form dibuka terlalu lama atau cookie diblokir; muat ulang halaman form lalu coba lagi |
| "Ukuran data yang dikirim melebihi batas server" | Naikkan `upload_max_filesize` dan `post_max_size` di `php.ini` |
| `Class ... not found` | Autoload belum ada, jalankan `composer dump-autoload` |

## 11. Simpan ke GitHub
```
git init
git add .
git commit -m "Project 5: CRUD kategori dan produk dengan MVC"
git branch -M main
git remote add origin https://github.com/USERNAME_ANDA/NAMA_REPO.git
git push -u origin main
```
`.gitignore` sudah mengecualikan `vendor/` dan foto yang diunggah.
