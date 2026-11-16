<?php

namespace App\Core;

class Upload
{
    private const MAKS_UKURAN = 2097152; // 2 MB

    private const TIPE = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    // Memvalidasi dan menyimpan gambar, mengembalikan nama file (null jika tidak ada file)
    public static function gambar($file): ?string
    {
        if (!is_array($file) || !isset($file['error']) || !is_int($file['error'])) {
            return null;
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException(match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran foto melebihi batas yang diizinkan.',
                default => 'Foto gagal diunggah, silakan coba lagi.',
            });
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \InvalidArgumentException('Berkas foto tidak valid.');
        }

        if ($file['size'] > self::MAKS_UKURAN) {
            throw new \InvalidArgumentException('Ukuran foto maksimal 2 MB.');
        }

        // Tipe file dibaca dari isinya, bukan dari nama atau ekstensi kiriman pengguna
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

        if (!isset(self::TIPE[$mime]) || @getimagesize($file['tmp_name']) === false) {
            throw new \InvalidArgumentException('Foto harus berupa gambar JPG, PNG, atau WEBP.');
        }

        $folder = ROOT . 'uploads/produk/';

        if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
            throw new \RuntimeException('Folder unggahan tidak dapat dibuat.');
        }

        // Nama acak supaya tidak bisa ditebak dan tidak menimpa file lain
        $nama = bin2hex(random_bytes(16)) . '.' . self::TIPE[$mime];

        if (!move_uploaded_file($file['tmp_name'], $folder . $nama)) {
            throw new \RuntimeException('Foto gagal disimpan.');
        }

        return $nama;
    }

    // Menghapus file gambar lama; nama dicek dulu agar hanya file buatan Upload::gambar() yang terhapus
    public static function hapus(?string $nama): void
    {
        if ($nama === null || !preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $nama)) {
            return;
        }

        $path = ROOT . 'uploads/produk/' . $nama;

        if (is_file($path)) {
            unlink($path);
        }
    }
}
