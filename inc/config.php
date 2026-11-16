<?php

// Laporan error
error_reporting(E_ALL);

// Mode pengembangan: true menampilkan detail error (Whoops).
// Ubah menjadi false ketika aplikasi dipublikasikan.
define("DEBUG", true);

// Zona waktu (WIB), dipakai untuk tanggal transaksi penjualan
date_default_timezone_set("Asia/Jakarta");

// Mulai sesi
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

// Url - sesuaikan dengan alamat project di komputer Anda
define("URL", "http://localhost/project5");
define("AST", URL . "/layouts/assets");

// Path root
define("ROOT", dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Koneksi database - sesuaikan dengan MySQL Anda (bawaan XAMPP: root tanpa password)
define("DB_HOST", "localhost");
define("DB_NAME", "dbproject5");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_PORT", 3307);

// Autoload
require_once ROOT . "vendor/autoload.php";

// Fungsi bantu (e, rupiah, csrf_field)
require_once ROOT . "inc/helpers.php";

if (DEBUG) {
    // Whoops
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
} else {
    // Detail error disimpan ke log, tidak ditampilkan ke pengunjung
    ini_set("display_errors", "0");
    set_exception_handler(function (\Throwable $e) {
        error_log($e);
        http_response_code(500);
        echo "Terjadi kesalahan pada server.";
    });
}
