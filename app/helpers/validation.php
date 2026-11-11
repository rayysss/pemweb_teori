<?php
/**
 * Helper validasi data input formulir.
 * Memastikan data yang dikirim pengguna memenuhi standar format sebelum diproses oleh model.
 */

/**
 * Memvalidasi apakah format string email sesuai standar RFC.
 */
function valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Memvalidasi panjang minimal password untuk keamanan akun (minimal 8 karakter).
 */
function valid_password(string $password): bool
{
    return strlen($password) >= 8;
}

/**
 * Memeriksa apakah input wajib telah diisi (tidak boleh string kosong atau spasi saja).
 */
function required_value(mixed $value): bool
{
    return trim((string) $value) !== '';
}

/**
 * Memvalidasi apakah suatu nilai merupakan bilangan bulat positif (> 0).
 */
function positive_int(mixed $value): bool
{
    return filter_var($value, FILTER_VALIDATE_INT) !== false && (int) $value > 0;
}
