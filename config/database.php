<?php
/**
 * Konfigurasi koneksi MySQL untuk ZORA Electronics.
 * File ini bertindak sebagai jembatan tunggal antara PHP dan database MySQL.
 */

// Kredensial koneksi database MySQL lokal XAMPP.
const DB_HOST = '127.0.0.1';
const DB_NAME = 'pemweb-teori';
const DB_USER = 'root';
const DB_PASS = '';

// Aturan ongkir — satuan: Rupiah.
const SHIPPING_FREE_THRESHOLD = 500000; // Gratis ongkir jika subtotal >= nilai ini.
const SHIPPING_FLAT_COST      = 15000;  // Ongkir flat jika subtotal < threshold.

/**
 * Menyediakan koneksi PDO tunggal (Reusable / Singleton Pattern sederhana).
 * Menghindari pembukaan banyak koneksi TCP yang membebani memori server.
 *
 * @return PDO Objek koneksi aktif ke MySQL.
 */
function db(): PDO
{
    // Variabel statis yang nilainya tetap tersimpan di memori selama satu HTTP request berjalan.
    static $pdo = null;

    // Jika koneksi sudah pernah dibuat sebelumnya, gunakan kembali koneksi yang sama.
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // DSN (Data Source Name): spesifikasi protokol, host, database, dan charset.
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    // Inisialisasi PDO dengan pengaturan keamanan dan performa terbaik.
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        // Mengubah error database menjadi Exception agar bisa ditangkap blok try-catch.
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        // Mengembalikan hasil query sebagai array asosiatif (nama_kolom => nilai).
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        // Mematikan emulasi prepared statement agar dikompilasi secara native oleh MySQL (Anti SQL Injection).
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
