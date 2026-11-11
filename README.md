# ZORA — Electronics Store

**ZORA** adalah proyek website e-commerce toko alat elektronik yang dibangun menggunakan PHP Native dengan pola arsitektur Model–View–Controller (MVC), MySQL/MariaDB, PDO, Bootstrap 5, CSS, dan JavaScript.

Proyek ini dibuat sebagai aplikasi toko elektronik dengan pengalaman belanja yang sederhana. Desain antarmuka mengambil inspirasi dari pendekatan visual retail UNIQLO: tata letak bersih, navigasi ringkas, ruang kosong yang cukup, hierarki teks yang jelas, serta fokus pada foto dan informasi produk. Identitas visual ZORA menggunakan kombinasi **biru, putih, dan abu-abu muda**, bukan merah-putih.

> **Status proyek:** aplikasi PHP dan database untuk pengembangan lokal. Fitur katalog, autentikasi, keranjang, checkout, pesanan, dan administrasi dasar sudah tersedia. Pembayaran masih berupa pencatatan metode dan status awal; belum terhubung ke payment gateway.

---

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Tujuan dan Konsep Desain](#2-tujuan-dan-konsep-desain)
3. [Teknologi yang Digunakan](#3-teknologi-yang-digunakan)
4. [Struktur Folder](#4-struktur-folder)
5. [Fitur Aplikasi](#5-fitur-aplikasi)
6. [Alur Penggunaan](#6-alur-penggunaan)
7. [Struktur dan Relasi Database](#7-struktur-dan-relasi-database)
8. [Persyaratan Sistem](#8-persyaratan-sistem)
9. [Instalasi di XAMPP](#9-instalasi-di-xampp)
10. [Konfigurasi Database](#10-konfigurasi-database)
11. [Akun Demo](#11-akun-demo)
12. [Daftar URL/Route](#12-daftar-urlroute)
13. [Penjelasan MVC](#13-penjelasan-mvc)
14. [Keamanan dan Validasi](#14-keamanan-dan-validasi)
15. [Batasan Fitur Saat Ini](#15-batasan-fitur-saat-ini)
16. [Troubleshooting](#16-troubleshooting)
17. [Rencana Pengembangan](#17-rencana-pengembangan)

---

## 1. Gambaran Umum

ZORA merupakan website e-commerce untuk menampilkan dan menjual berbagai perangkat elektronik. Pengunjung dapat melihat kategori dan katalog produk, mencari produk, mengurutkan produk berdasarkan harga atau nama, serta membuka halaman detail produk.

Pengguna yang sudah memiliki akun dapat mengelola keranjang belanja, melakukan checkout, memilih metode pembayaran, dan melihat riwayat serta detail pesanan. Administrator memiliki halaman khusus untuk melihat ringkasan toko, menambahkan produk, mengarsipkan produk, dan memperbarui status pesanan.

Aplikasi menggunakan pendekatan **server-rendered PHP**. Browser mengirimkan permintaan ke `public/index.php`, router menentukan controller yang menangani permintaan, model berinteraksi dengan database, lalu view menampilkan hasilnya.

### Identitas proyek

| Informasi | Keterangan |
|---|---|
| Nama aplikasi | ZORA Electronics Store |
| Jenis aplikasi | Website e-commerce / toko elektronik |
| Bahasa utama | PHP |
| Arsitektur | Model–View–Controller (MVC) sederhana |
| Database | MySQL / MariaDB |
| Antarmuka | Bootstrap 5, CSS khusus, JavaScript |
| Mata uang | Rupiah (IDR) |
| Bahasa antarmuka | Bahasa Indonesia |
| Lingkungan pengembangan | XAMPP / localhost |

## 2. Tujuan dan Konsep Desain

### Tujuan aplikasi

- Menyediakan katalog produk elektronik dalam satu website.
- Mempermudah pelanggan mencari dan memilih produk.
- Menyediakan proses keranjang dan checkout yang terstruktur.
- Menyimpan data pengguna, produk, keranjang, pesanan, dan pembayaran pada MySQL.
- Menyediakan halaman administrasi untuk mengelola aktivitas dasar toko.

### Prinsip tampilan

Desain ZORA dibuat minimalis agar pengguna tidak menerima terlalu banyak informasi sekaligus.

- **Navigasi sederhana:** menu utama berisi akses ke katalog dan kategori populer, akun, serta keranjang.
- **Fokus pada produk:** gambar, nama, kategori, dan harga menjadi informasi utama pada kartu produk.
- **Hierarki visual:** judul, deskripsi singkat, label, dan tombol dibedakan secara konsisten.
- **Ruang kosong:** jarak antarelemen digunakan agar halaman terasa lebih ringan dan mudah dipindai.
- **Responsif:** grid produk, navigasi, dan formulir menyesuaikan ukuran layar.

### Palet warna

| Penggunaan | Warna |
|---|---|
| Biru utama | `#174A82` |
| Biru gelap | `#10345D` |
| Biru muda | `#EAF1F9` |
| Teks utama | `#202B38` |
| Teks sekunder | `#6E7A87` |
| Garis/border | `#E4E9EF` |
| Latar lembut | `#F7F9FB` |
| Putih | `#FFFFFF` |

Variabel warna utama berada di `public/assets/css/style.css`, sehingga identitas warna dapat disesuaikan dari satu tempat.

## 3. Teknologi yang Digunakan

| Teknologi | Peran dalam proyek |
|---|---|
| PHP 8.0+ | Logika aplikasi, controller, model, dan rendering halaman |
| MySQL / MariaDB | Penyimpanan data aplikasi |
| PDO | Koneksi database dan prepared statements |
| Bootstrap 5.3.3 | Komponen dasar dan utilitas antarmuka |
| CSS3 | Identitas visual, layout, warna, dan responsivitas |
| JavaScript | Interaksi navigasi, keranjang, filter, dan checkout |
| Apache | Web server lokal melalui XAMPP |
| Google Fonts | Tipografi DM Sans dan Manrope |
| Unsplash | Sumber gambar produk dan gambar hero contoh |

Bootstrap, Bootstrap Icons, Google Fonts, dan gambar Unsplash dipanggil melalui URL eksternal/CDN. Koneksi internet diperlukan agar aset eksternal tersebut dapat dimuat.

## 4. Struktur Folder

Struktur direktori proyek mengikuti pemisahan tanggung jawab MVC dan susunan folder yang digunakan selama pengembangan.

```text
ZORA/
│
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── OrderController.php
│   │   └── AdminController.php
│   │
│   ├── models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Cart.php
│   │   ├── Order.php
│   │   └── Payment.php
│   │
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── header.php
│   │   │   ├── navbar.php
│   │   │   └── footer.php
│   │   ├── home/
│   │   │   ├── index.php
│   │   │   └── not-found.php
│   │   ├── products/
│   │   │   ├── index.php
│   │   │   └── detail.php
│   │   ├── cart/
│   │   │   └── index.php
│   │   ├── checkout/
│   │   │   ├── index.php
│   │   │   └── success.php
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   └── register.php
│   │   ├── account/
│   │   │   ├── index.php
│   │   │   ├── orders.php
│   │   │   └── order.php
│   │   └── admin/
│   │       ├── index.php
│   │       └── products.php
│   │
│   └── helpers/
│       ├── auth.php
│       ├── validation.php
│       └── functions.php
│
├── config/
│   └── database.php
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│       ├── css/
│       │   ├── style.css
│       │   ├── responsive.css
│       │   └── admin.css
│       ├── js/
│       │   ├── main.js
│       │   ├── cart.js
│       │   ├── filter.js
│       │   └── checkout.js
│       └── images/
│           └── zora-mark.svg
│
├── storage/
│   └── uploads/
│       ├── products/
│       └── payments/
│
├── .htaccess
├── .gitignore
├── zora.sql
└── README.md
```

### Fungsi direktori dan file penting

- `app/controllers/`: menerima permintaan halaman atau aksi pengguna, memanggil model, lalu menentukan view yang ditampilkan.
- `app/models/`: menyimpan query dan operasi data yang berhubungan dengan tabel MySQL.
- `app/views/`: berisi template halaman yang dilihat pengguna. Folder `layouts` menyimpan header, navigasi, dan footer bersama.
- `app/helpers/`: fungsi bantuan untuk autentikasi, validasi, URL, format Rupiah, flash message, rendering, dan CSRF.
- `config/database.php`: konfigurasi koneksi PDO ke database.
- `public/`: satu-satunya direktori yang menjadi pintu masuk web. `index.php` bertindak sebagai front controller.
- `public/assets/`: stylesheet, JavaScript, dan gambar identitas ZORA.
- `storage/uploads/`: lokasi yang disiapkan untuk file gambar produk dan bukti pembayaran. Upload belum menjadi alur aktif pada versi ini.
- `zora.sql`: skrip pembuatan database, tabel, indeks, relasi, kategori, produk contoh, dan akun administrator.

## 5. Fitur Aplikasi

### 5.1 Pengunjung dan katalog produk

- Melihat homepage ZORA, kategori, dan produk pilihan.
- Melihat seluruh produk yang berstatus aktif.
- Mencari produk berdasarkan nama, deskripsi, atau SKU.
- Memfilter katalog berdasarkan kategori.
- Mengurutkan produk berdasarkan harga terendah, harga tertinggi, atau nama A–Z.
- Membuka detail produk berdasarkan slug.
- Melihat harga, deskripsi, warna, SKU, serta informasi stok yang tersedia pada data produk.

### 5.2 Akun pelanggan

- Membuat akun melalui halaman registrasi.
- Masuk menggunakan email dan password.
- Keluar melalui tombol logout.
- Melihat informasi akun.
- Melihat daftar pesanan dan detail masing-masing pesanan.
- Checkout hanya dapat dilakukan oleh pengguna yang sudah login.

Password pengguna disimpan menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`.

### 5.3 Keranjang belanja

- Menambahkan produk ke keranjang.
- Mengubah kuantitas barang.
- Menghapus item dari keranjang.
- Menghitung subtotal berdasarkan harga yang tersimpan pada item keranjang.
- Menyimpan keranjang di database, termasuk untuk pengunjung yang belum login melalui session ID.
- Menggabungkan keranjang tamu ke keranjang akun ketika pengguna login atau mendaftar.

### 5.4 Checkout dan pesanan

- Checkout membutuhkan akun pelanggan dan keranjang yang tidak kosong.
- Pelanggan mengisi nama penerima, nomor telepon, alamat, kota, provinsi, kode pos, dan catatan (opsional).
- Pelanggan memilih metode pembayaran yang tersedia.
- Sistem menghitung subtotal, biaya pengiriman, dan total pembayaran.
- Sistem memeriksa stok kembali sebelum membuat pesanan.
- Data pesanan dan detail barang disimpan ke database.
- Stok produk dikurangi ketika pesanan berhasil dibuat.
- Keranjang dikosongkan setelah transaksi database berhasil.
- Pelanggan dapat melihat riwayat dan detail pesanan.

**Aturan ongkos kirim saat ini:** Rp15.000 untuk subtotal di bawah Rp500.000 dan gratis ongkir untuk subtotal minimal Rp500.000. Aturan dapat diubah pada `app/models/Order.php` dan tampilan checkout.

### 5.5 Administrasi

Administrator dapat:

- Melihat ringkasan jumlah produk aktif, jumlah pesanan, jumlah pelanggan, dan omzet pesanan dengan status yang diperhitungkan.
- Melihat daftar pesanan terbaru.
- Mengubah status pesanan.
- Menambahkan produk dengan nama, SKU, kategori, harga, stok, warna, tag, URL gambar, deskripsi, dan pilihan produk unggulan.
- Mengarsipkan produk agar tidak lagi tampil dalam katalog aktif.

Pengelolaan produk pada versi saat ini berupa **tambah dan arsip**, belum mencakup edit produk. Pengelolaan kategori, pengguna, dan verifikasi pembayaran juga belum tersedia di dashboard.

## 6. Alur Penggunaan

### Alur pelanggan

1. Pengunjung membuka homepage ZORA.
2. Pengunjung membuka katalog atau memilih kategori.
3. Pengunjung mencari dan membuka detail produk.
4. Pengunjung menambahkan produk ke keranjang.
5. Pengunjung login atau membuat akun.
6. Pengunjung membuka checkout dan melengkapi informasi penerima.
7. Sistem memvalidasi stok dan membuat pesanan.
8. Sistem menyimpan item pesanan dan data pembayaran dengan status awal `pending`.
9. Pelanggan melihat halaman keberhasilan dan dapat memeriksa pesanan melalui menu Pesanan.

### Alur administrator

1. Administrator login menggunakan akun dengan role `admin`.
2. Administrator membuka dashboard.
3. Administrator menambahkan produk atau mengarsipkan produk yang tidak dijual.
4. Administrator melihat pesanan terbaru.
5. Administrator memperbarui status pesanan sesuai proses operasional toko.

### Pengelolaan stok

Ketika checkout berhasil, stok produk dikurangi sesuai kuantitas pesanan. Jika administrator mengubah status pesanan menjadi `cancelled`, sistem mengembalikan stok berdasarkan item pesanan. Pesanan yang telah dibatalkan tidak dapat diaktifkan kembali melalui fungsi status yang tersedia.

## 7. Struktur dan Relasi Database

Nama database: **`zora`**  
Charset: `utf8mb4`  
Collation: `utf8mb4_unicode_ci`  
Storage engine: InnoDB.

| No. | Tabel | Kegunaan |
|---:|---|---|
| 1 | `users` | Data pelanggan dan administrator, termasuk email, password hash, role, dan status akun. |
| 2 | `categories` | Kategori produk seperti Smartphones, Computers, Audio, Wearables, Cameras, dan Accessories. |
| 3 | `products` | Informasi produk: kategori, nama, SKU, deskripsi, harga, stok, warna, gambar, status, dan penanda produk unggulan. |
| 4 | `product_images` | Struktur untuk menyimpan foto tambahan/galeri produk. |
| 5 | `user_addresses` | Struktur untuk menyimpan alamat pengguna. |
| 6 | `carts` | Keranjang aktif milik pengguna atau sesi pengunjung. |
| 7 | `cart_items` | Produk, kuantitas, dan snapshot harga pada keranjang. |
| 8 | `orders` | Data utama pesanan, penerima, alamat, subtotal, ongkir, total, dan status pesanan. |
| 9 | `order_items` | Snapshot nama, SKU, foto, harga, kuantitas, dan total setiap produk pada pesanan. |
| 10 | `payments` | Metode, nominal, status pembayaran, dan kolom yang disiapkan untuk referensi/bukti pembayaran. |
| 11 | `wishlist_items` | Struktur untuk menyimpan produk favorit pengguna. |

### Relasi utama

- Satu kategori dapat memiliki banyak produk; produk dapat tidak memiliki kategori (`category_id` boleh `NULL`).
- Satu pengguna dapat memiliki beberapa alamat, keranjang, dan pesanan.
- Satu keranjang memiliki beberapa `cart_items`; setiap item merujuk pada satu produk.
- Satu pesanan memiliki beberapa `order_items`.
- Satu pesanan memiliki data pembayaran pada tabel `payments`.
- Wishlist menghubungkan pengguna dan produk.

Relasi database menggunakan foreign key. Penghapusan beberapa data induk menerapkan aturan `CASCADE`, `SET NULL`, atau `RESTRICT` sesuai kebutuhan untuk menjaga konsistensi data.

### Data contoh

Skrip `zora.sql` memasukkan enam kategori dan delapan produk awal, antara lain Nova Phone X, AirBook 14, Studio Headphones, Motion Watch, Room Speaker Mini, Pocket Camera, Tab S Lite, dan Precision Mouse.

**Peringatan:** skrip `zora.sql` menggunakan `DROP TABLE IF EXISTS` untuk tabel-tabel ZORA sebelum membuat ulang struktur. Import ulang akan menghapus data lama pada tabel tersebut. Lakukan backup terlebih dahulu jika database sudah berisi data penting.

## 8. Persyaratan Sistem

- Windows dengan XAMPP atau lingkungan setara.
- Apache aktif.
- PHP **8.0 atau lebih baru**; proyek diuji sintaks dengan PHP 8.2.
- MySQL atau MariaDB yang mendukung InnoDB dan foreign key.
- Ekstensi PHP `pdo_mysql` aktif.
- `mod_rewrite` Apache aktif untuk URL bersih.
- Browser modern seperti Chrome, Edge, Firefox, atau Safari.
- Koneksi internet untuk Bootstrap CDN, Bootstrap Icons, Google Fonts, dan gambar produk eksternal.

## 9. Instalasi di XAMPP

### Langkah 1 — Ekstrak proyek

Ekstrak folder `ZORA` ke:

```text
C:\xampp\htdocs\ZORA
```

Pastikan file utama berada di:

```text
C:\xampp\htdocs\ZORA\public\index.php
```

### Langkah 2 — Jalankan server

Buka XAMPP Control Panel, lalu aktifkan:

- Apache
- MySQL

Pastikan keduanya berjalan tanpa konflik port.

### Langkah 3 — Buat database

1. Buka `http://localhost/phpmyadmin`.
2. Pilih tab **Import**.
3. Klik **Choose File** dan pilih `C:\xampp\htdocs\ZORA\zora.sql`.
4. Klik **Import** atau **Go**.
5. Pastikan database `zora` muncul di panel kiri dan tabel berhasil dibuat.

Skrip SQL membuat database `zora` secara otomatis, jadi tidak perlu membuat database secara manual sebelum import.

### Langkah 4 — Periksa koneksi database

Buka `config/database.php`. Konfigurasi default untuk instalasi XAMPP standar adalah:

```php
const DB_HOST = '127.0.0.1';
const DB_NAME = 'zora';
const DB_USER = 'root';
const DB_PASS = '';
```

Sesuaikan `DB_USER` dan `DB_PASS` bila MySQL lokal menggunakan username atau password berbeda.

### Langkah 5 — Buka aplikasi

Gunakan URL:

```text
http://localhost/ZORA/public/
```

**Penting:** untuk struktur proyek ini, halaman utama berada di folder `public`. Karena itu, gunakan URL `/ZORA/public/`, bukan `/ZORA/`. Akses langsung ke `http://localhost/ZORA/` dapat menampilkan `403 Forbidden` karena folder root tidak memiliki file index publik dan directory listing dinonaktifkan. Ini merupakan perilaku proteksi direktori, bukan bukti bahwa database gagal.

Jika Apache dan `mod_rewrite` berjalan normal, URL aplikasi dapat digunakan seperti:

```text
http://localhost/ZORA/public/products
http://localhost/ZORA/public/cart
http://localhost/ZORA/public/login
```

## 10. Konfigurasi Database

Koneksi database dikelola oleh fungsi `db()` pada `config/database.php`. Fungsi tersebut membuat satu koneksi PDO dan menggunakan kembali koneksi yang sama selama satu request.

Konfigurasi PDO meliputi:

- `PDO::ERRMODE_EXCEPTION`: error database dilempar sebagai exception.
- `PDO::FETCH_ASSOC`: hasil query dikembalikan sebagai array asosiatif.
- `PDO::EMULATE_PREPARES = false`: menggunakan prepared statement native bila didukung driver.
- `charset=utf8mb4`: mendukung karakter Unicode.

Controller dan model mengakses koneksi melalui `db()`.

## 11. Akun Demo

Skrip database menyediakan akun administrator untuk pengujian lokal:

```text
Email    : admin@zora.test
Password : AdminZora123!
Role     : admin
```

Akses login:

```text
http://localhost/ZORA/public/login
```

Akses dashboard setelah login:

```text
http://localhost/ZORA/public/admin
```

Akun tersebut hanya untuk pengembangan lokal. Jangan gunakan password demo pada server publik. Ganti password dan tinjau akun demo sebelum deployment.

## 12. Daftar URL/Route

Semua route diproses oleh `public/index.php`.

| URL (relatif terhadap `/ZORA/public/`) | Metode umum | Fungsi | Akses |
|---|---|---|---|
| `/` atau `/home` | GET | Homepage | Publik |
| `/products` | GET | Katalog, pencarian, filter, dan pengurutan | Publik |
| `/products/{slug}` | GET | Detail produk | Publik |
| `/login` | GET, POST | Login | Publik |
| `/register` | GET, POST | Registrasi | Publik |
| `/logout` | POST | Logout | Login |
| `/cart` | GET | Melihat keranjang | Publik |
| `/cart/add` | POST | Menambahkan produk | Publik |
| `/cart/update` | POST | Mengubah kuantitas | Publik |
| `/cart/remove` | POST | Menghapus item | Publik |
| `/checkout` | GET, POST | Form checkout dan pembuatan pesanan | Login |
| `/account` | GET | Ringkasan akun | Login |
| `/orders` | GET | Riwayat pesanan | Login |
| `/orders/{id}` | GET | Detail pesanan milik pengguna | Login |
| `/admin` | GET | Dashboard admin | Admin |
| `/admin/products` | GET | Daftar produk admin | Admin |
| `/admin/products/create` | POST | Menambahkan produk | Admin |
| `/admin/products/delete` | POST | Mengarsipkan produk | Admin |
| `/admin/orders/update` | POST | Memperbarui status pesanan | Admin |

Untuk operasi yang mengubah data, form menggunakan token CSRF dan metode POST.

## 13. Penjelasan MVC

### Model

Model bertanggung jawab pada akses dan pengolahan data database.

| Model | Tanggung jawab |
|---|---|
| `User.php` | Mencari akun, membuat akun, autentikasi, dan memperbarui data profil. |
| `Product.php` | Mengambil katalog, produk unggulan, detail berdasarkan slug, menambah, dan mengarsipkan produk. |
| `Category.php` | Mengambil daftar kategori dan detail kategori. |
| `Cart.php` | Membuat/mengambil keranjang, menambah item, mengubah jumlah, menghapus item, menghitung subtotal, dan menggabungkan keranjang tamu. |
| `Order.php` | Membuat pesanan dari keranjang, mengambil riwayat/detail pesanan, dan memperbarui status. |
| `Payment.php` | Mencatat metode serta status awal pembayaran dan mengambil data pembayaran pesanan. |

### Controller

Controller menerima permintaan dari router, menjalankan validasi dan pemeriksaan akses, memanggil model, kemudian menampilkan view atau melakukan redirect.

| Controller | Tanggung jawab |
|---|---|
| `HomeController.php` | Menyiapkan homepage, kategori, dan produk unggulan. |
| `AuthController.php` | Registrasi, login, logout, dan penggabungan keranjang tamu setelah login/registrasi. |
| `ProductController.php` | Katalog, pencarian, filter, pengurutan, dan detail produk. |
| `CartController.php` | Menampilkan dan memproses perubahan keranjang. |
| `CheckoutController.php` | Memvalidasi data checkout dan membuat pesanan. |
| `OrderController.php` | Menampilkan riwayat dan detail pesanan milik pelanggan. |
| `AdminController.php` | Dashboard admin, penambahan/arsip produk, dan perubahan status pesanan. |

### View

View adalah template HTML/PHP yang menampilkan data dari controller. Layout `header.php`, `navbar.php`, dan `footer.php` digunakan bersama agar tampilan ant halaman konsisten.

### Helper

- `functions.php`: URL, asset, redirect, escaping HTML, format Rupiah, slug, flash message, rendering view, dan CSRF.
- `auth.php`: pengguna aktif, pemeriksaan login, dan pembatasan akses admin.
- `validation.php`: pemeriksaan email, password, input wajib, dan bilangan positif.

### Front controller dan autoload

`public/index.php` melakukan `session_start()`, memuat konfigurasi dan helper, mendaftarkan autoloader sederhana untuk model/controller, memeriksa koneksi database, membaca URL, lalu mengarahkan request ke controller yang sesuai.

## 14. Keamanan dan Validasi

Fitur keamanan yang sudah diterapkan pada kode:

- Query database menggunakan PDO prepared statements pada operasi yang menerima input.
- Password disimpan menggunakan `password_hash()` dan diverifikasi menggunakan `password_verify()`.
- Session ID diperbarui setelah login dan registrasi menggunakan `session_regenerate_id(true)`.
- Form yang mengubah data menggunakan token CSRF.
- Output dinamis pada template di-escape menggunakan helper `e()` yang memanggil `htmlspecialchars()`.
- Halaman checkout mensyaratkan login.
- Halaman admin memeriksa role pengguna melalui `require_admin()`.
- Detail pesanan pelanggan dibatasi berdasarkan ID pengguna yang sedang login.
- Direktori `app`, `config`, dan `storage` diblokir dari akses HTTP langsung melalui `.htaccess` root.
- Listing direktori dinonaktifkan dengan `Options -Indexes`.
- Pembuatan pesanan memeriksa stok di dalam transaksi database dan menggunakan row lock (`FOR UPDATE`).

**Catatan:** perlindungan `.htaccess` bergantung pada konfigurasi Apache. Pada deployment, document root sebaiknya diarahkan langsung ke folder `public`, gunakan HTTPS, matikan tampilan error detail, atur permission file, validasi semua file upload, serta lakukan backup rutin.

## 15. Batasan Fitur Saat Ini

Bagian ini penting agar status aplikasi tidak disalahartikan sebagai sistem produksi yang sudah lengkap.

1. **Payment gateway belum tersedia.** Metode transfer bank, QRIS, e-wallet, dan COD dicatat sebagai pilihan pada tabel pembayaran. Status awal pembayaran adalah `pending`; belum ada integrasi Midtrans, Xendit, atau penyedia pembayaran lainnya.
2. **Upload bukti pembayaran belum aktif.** Kolom `proof_image` dan folder `storage/uploads/payments` sudah disiapkan, tetapi alur upload dan verifikasi belum dibuat.
3. **Upload foto produk belum aktif.** Admin saat ini memasukkan URL gambar produk. Folder `storage/uploads/products` disiapkan untuk pengembangan berikutnya.
4. **Manajemen produk belum CRUD penuh.** Fitur yang tersedia adalah tambah dan arsip produk; edit produk belum tersedia.
5. **Manajemen kategori belum tersedia di dashboard.** Kategori saat ini berasal dari seed database atau perubahan langsung melalui database.
6. **Manajemen alamat belum lengkap.** Tabel `user_addresses` sudah dibuat, tetapi halaman CRUD alamat belum tersedia. Informasi pengiriman pada checkout disimpan sebagai snapshot di pesanan.
7. **Wishlist belum memiliki antarmuka aktif.** Tabel `wishlist_items` sudah disiapkan untuk pengembangan berikutnya.
8. **Ongkos kirim masih simulasi.** Tarif tetap Rp15.000 dan gratis untuk subtotal minimal Rp500.000; belum ada integrasi kurir atau pelacakan pengiriman.
9. **Gambar contoh menggunakan URL eksternal.** Jika URL gambar tidak dapat diakses atau tidak ada koneksi internet, gambar dapat gagal ditampilkan.
10. **Belum dilakukan audit keamanan dan pengujian beban untuk produksi.** Pengujian saat ini ditujukan untuk pengembangan dan pembelajaran di lingkungan lokal.

## 16. Troubleshooting

### A. Muncul `403 Forbidden` saat membuka `http://localhost/ZORA/`

Buka URL yang sesuai dengan lokasi front controller:

```text
http://localhost/ZORA/public/
```

Folder root proyek tidak ditujukan untuk menjadi document root pada konfigurasi lokal ini. `Options -Indexes` menonaktifkan daftar isi direktori, sehingga membuka folder tanpa index publik dapat menghasilkan 403.

### B. Pesan “Koneksi database ZORA gagal”

Periksa hal berikut:

1. MySQL sudah aktif di XAMPP.
2. Database `zora` sudah diimpor melalui phpMyAdmin.
3. Nama database, username, dan password pada `config/database.php` sesuai dengan instalasi lokal.
4. Ekstensi `pdo_mysql` aktif di PHP.

### C. URL selain homepage menghasilkan 404 atau tidak terbaca

Pastikan `mod_rewrite` aktif dan konfigurasi Apache mengizinkan `.htaccess` melalui `AllowOverride All` untuk direktori proyek. Setelah mengubah `httpd.conf`, restart Apache.

### D. Gambar atau font tidak tampil

Aset contoh memerlukan koneksi internet karena sebagian menggunakan CDN, Google Fonts, dan Unsplash. Periksa koneksi, URL gambar, serta pemblokir konten pada browser.

### E. Login admin gagal

Pastikan skrip `zora.sql` yang digunakan berasal dari paket proyek ini dan akun `admin@zora.test` tersedia pada tabel `users`. Gunakan email dan password demo yang tercantum pada [Akun Demo](#11-akun-demo). Jangan mengubah kolom password secara manual menjadi teks biasa karena aplikasi menggunakan verifikasi hash.

### F. Pesan token CSRF tidak valid atau sesi formulir kedaluwarsa

Muat ulang halaman, isi kembali form, lalu kirim ulang. Token CSRF terikat pada session browser.

## 17. Rencana Pengembangan

Pengembangan berikutnya dapat dilakukan secara bertahap:

- Menambahkan fitur edit produk dan pengelolaan kategori pada dashboard admin.
- Mengaktifkan upload gambar produk dengan validasi tipe, ukuran, dan nama file.
- Membuat halaman CRUD alamat pelanggan.
- Mengembangkan wishlist untuk pelanggan.
- Mengintegrasikan payment gateway dan webhook konfirmasi pembayaran.
- Menambahkan upload bukti pembayaran dan halaman verifikasi admin jika memakai transfer manual.
- Mengintegrasikan ongkos kirim dan pelacakan kurir.
- Menambahkan pagination katalog, filter rentang harga, dan pencarian yang lebih lengkap.
- Menambah laporan penjualan, ekspor data, dan audit aktivitas admin.
- Menulis pengujian otomatis untuk autentikasi, keranjang, checkout, stok, serta status pesanan.

---

## Lisensi dan Penggunaan

Proyek ini disusun untuk kebutuhan pembelajaran dan pengembangan website e-commerce ZORA. Pastikan seluruh gambar, logo, font, dan aset eksternal yang digunakan pada versi publik memiliki hak penggunaan yang sesuai.

**ZORA — Electronics for Everyday Life.**
