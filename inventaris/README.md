# CRUD Inventaris — Tugas Rutin 8

Aplikasi pengelolaan inventaris berbasis PHP dan MySQL untuk memenuhi Tugas Rutin 8.

## Deskripsi

Aplikasi ini digunakan untuk mengelola data produk, kategori, dan supplier melalui operasi CRUD (Create, Read, Update, Delete).

## Fitur

- Menampilkan daftar produk.
- Menambahkan produk baru.
- Mengedit data produk.
- Menghapus produk dengan konfirmasi.
- Menghubungkan produk dengan kategori dan supplier.
- Menggunakan PDO untuk koneksi database.
- Menggunakan Prepared Statements untuk query.
- Mengamankan output dengan `htmlspecialchars()`.
- Menampilkan notifikasi proses.

## Teknologi

- PHP
- MySQL
- HTML
- CSS
- PDO
- Git dan GitHub

## Database

Nama database: `inventaris_db`

Tabel utama:
- `kategori`
- `supplier`
- `produk`

## Struktur Proyek

- `config/` — konfigurasi koneksi database.
- `screenshots/` — dokumentasi tampilan aplikasi.
- `index.php` — halaman utama.
- `create.php` — formulir tambah produk.
- `store.php` — proses penyimpanan produk.
- `edit.php` — formulir edit produk.
- `update.php` — proses pembaruan produk.
- `delete.php` — proses penghapusan produk.
- `export.php` — fitur ekspor data jika tersedia.

## Cara Menjalankan

1. Instal dan jalankan XAMPP.
2. Aktifkan Apache dan MySQL.
3. Letakkan folder proyek di `htdocs`.
4. Buat atau impor database `inventaris_db` melalui phpMyAdmin.
5. Sesuaikan konfigurasi koneksi di `config/Database.php`.
6. Buka `http://localhost/inventaris/` melalui browser.

## Dokumentasi Tampilan

Screenshot aplikasi tersedia di folder `screenshots/`.

## Pengembang

Syahru Ramadhan Nasution

Mahasiswa Ilmu Komputer — Universitas Negeri Medan

## Tujuan

Proyek ini dibuat untuk keperluan pembelajaran dan pemenuhan tugas akademik.