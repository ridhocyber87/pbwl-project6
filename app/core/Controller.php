<?php

namespace App\Core;

class Controller
{
	// Layout home
	protected function guest($view, $data = [])
	{
		require ROOT . "layouts/guest.php";
	}

	// Layout dashboard
	protected function dashboard($view, $data = [])
	{
		// Pesan sekali tampil dari proses sebelumnya
		$data['flash'] = $_SESSION['flash'] ?? null;
		unset($_SESSION['flash']);

		require ROOT . "layouts/dashboard.php";
	}

	// Menyimpan pesan untuk ditampilkan pada halaman berikutnya
	protected function flash(string $teks, string $tipe = 'success'): void
	{
		$_SESSION['flash'] = ['teks' => $teks, 'tipe' => $tipe];
	}

	protected function redirect(string $tujuan): never
	{
		header('Location: ' . URL . '/' . ltrim($tujuan, '/'));
		exit;
	}

	protected function notFound(): never
	{
		(new Error())->fileNotFound();
		exit;
	}

	// Aksi yang mengubah data harus lewat POST dengan token CSRF yang sah
	protected function wajibPost(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(405);
			header('Allow: POST');
			exit('Metode tidak diizinkan.');
		}

		// Data kosong padahal ada kiriman: ukuran melebihi batas post_max_size
		if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
			http_response_code(413);
			exit('Ukuran data yang dikirim melebihi batas server.');
		}

		if (!Csrf::valid($_POST['csrf_token'] ?? null)) {
			http_response_code(403);
			exit('Token formulir tidak valid. Kembali, muat ulang halaman, lalu coba lagi.');
		}
	}

	// Mengambil input POST sebagai teks yang sudah di-trim
	protected function post(string $key): string
	{
		$nilai = $_POST[$key] ?? '';

		return is_string($nilai) ? trim($nilai) : '';
	}

	// Memastikan id berupa angka bulat positif, jika tidak tampilkan 404
	protected function ambilId($id): int
	{
		if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id < 1) {
			$this->notFound();
		}

		return (int) $id;
	}

	// Kode error MySQL, misal 1062 (data ganda) atau 1451 (masih dipakai tabel lain)
	protected function kodeError(\PDOException $e): int
	{
		return (int) ($e->errorInfo[1] ?? 0);
	}
}
