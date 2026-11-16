# ShoesDens – Aplikasi E-Commerce Sepatu

ShoesDens adalah aplikasi e-commerce berbasis web yang dikembangkan menggunakan Laravel 10 dan MySQL. Aplikasi ini menyediakan fasilitas pembelian dan pengelolaan produk sepatu secara online.

## Fitur Aplikasi

### Pembeli
- Registrasi, login, dan logout.
- Melihat katalog dan detail produk.
- Mengelola keranjang belanja.
- Melakukan checkout dan pemesanan.
- Mengunggah bukti pembayaran.
- Melihat status pesanan dan pengiriman.
- Memberikan ulasan dan rating produk.

### Admin
- Login sebagai admin.
- Mengelola produk dan stok.
- Mengelola pesanan.
- Memverifikasi bukti pembayaran.
- Memperbarui status pesanan dan pengiriman.
- Melihat laporan penjualan.

## Teknologi
- PHP
- Laravel 10
- MySQL
- HTML, CSS, dan JavaScript
- Vite
- Composer
- Node.js dan npm

## Persyaratan Sistem
- PHP 8.1 atau lebih baru, sesuai kompatibilitas dependensi.
- Composer
- MySQL
- Node.js dan npm
- Web server Apache atau Nginx

## Instalasi Lokal

1. Clone repository:

   ```bash
   git clone https://github.com/Alakdan88/Laravel-ShoesDens.git
   ```

2. Masuk ke folder project:

   ```bash
   cd Laravel-ShoesDens
   ```

3. Instal dependensi PHP:

   ```bash
   composer install
   ```

4. Instal dependensi frontend:

   ```bash
   npm install
   ```

5. Salin `.env.example` menjadi `.env`, kemudian sesuaikan konfigurasi database lokal.

6. Buat application key:

   ```bash
   php artisan key:generate
   ```

7. Jalankan migrasi database setelah database lokal disiapkan:

   ```bash
   php artisan migrate
   ```

8. Buat symbolic link untuk file publik:

   ```bash
   php artisan storage:link
   ```

9. Bangun aset frontend:

   ```bash
   npm run build
   ```

10. Jalankan aplikasi:

    ```bash
    php artisan serve
    ```

11. Buka `http://127.0.0.1:8000` di browser.

## Konfigurasi Environment

Sesuaikan pengaturan berikut di file `.env` lokal:

- `APP_NAME`
- `APP_ENV`
- `APP_KEY`
- `APP_DEBUG`
- `APP_URL`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Jangan mengunggah file `.env` atau kredensial database ke repository publik.

## Keamanan Aplikasi

Aspek keamanan yang diterapkan dan akan diuji meliputi:
- Autentikasi pengguna.
- Pembatasan akses berdasarkan role.
- Validasi input.
- Perlindungan CSRF Laravel.
- Penggunaan hashing password.
- Pengaturan konfigurasi melalui environment.
- Pengujian SQL Injection, XSS, IDOR, dan keamanan sesi.

Hasil pengujian keamanan akan didokumentasikan dalam laporan proyek.

## Deployment

URL aplikasi: Akan ditambahkan setelah deployment selesai.

## Penggunaan AI

AI digunakan sebagai alat bantu pembelajaran, konsultasi, debugging, dan evaluasi implementasi selama pengembangan. Seluruh implementasi dan pengujian akhir tetap diperiksa oleh pengembang.

## Pengembang

Proyek individu untuk mata kuliah Cyber Security.
