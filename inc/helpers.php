<?php

// Mencetak teks dengan aman di view (mencegah XSS)
function e($teks): string
{
    return htmlspecialchars((string) ($teks ?? ''), ENT_QUOTES, 'UTF-8');
}

// Format angka menjadi rupiah, contoh: Rp 15.000
function rupiah($angka): string
{
    return "Rp " . number_format((int) $angka, 0, ",", ".");
}

// Format tanggal dari database (Y-m-d) menjadi d-m-Y
function tanggal($tgl): string
{
    $waktu = strtotime((string) $tgl);

    return $waktu ? date("d-m-Y", $waktu) : "";
}

// Input tersembunyi berisi token CSRF untuk setiap formulir POST
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(\App\Core\Csrf::token()) . '">';
}
