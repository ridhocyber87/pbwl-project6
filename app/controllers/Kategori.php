<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Kategori as KategoriModel;

class Kategori extends Controller
{

    private KategoriModel $model;

    public function __construct()
    {
        $this->model = new KategoriModel();
    }

    public function index()
    {
        $data['rows'] = $this->model->tampil();
        $this->dashboard('kategori/index', $data);
    }

    public function input()
    {
        $this->dashboard('kategori/input');
    }

    public function simpan()
    {
        $this->wajibPost();

        $input = $this->ambilInput();
        $errors = $this->validasi($input);

        if (!$errors) {
            try {
                $this->model->simpan($input);
                $this->flash('Kategori berhasil disimpan.');
                $this->redirect('kategori');
            } catch (\PDOException $e) {
                $errors[] = $this->pesanDatabase($e);
            }
        }

        $this->dashboard('kategori/input', ['old' => $input, 'errors' => $errors]);
    }

    public function edit($id)
    {
        $row = $this->model->edit($this->ambilId($id));

        if ($row === false) {
            $this->flash('Data kategori tidak ditemukan.', 'danger');
            $this->redirect('kategori');
        }

        $this->dashboard('kategori/edit', ['row' => $row]);
    }

    public function update()
    {
        $this->wajibPost();

        $id = $this->ambilId($_POST['kat_id'] ?? '');
        $input = $this->ambilInput();
        $errors = $this->validasi($input);

        if (!$errors) {
            try {
                $this->model->update($id, $input);
                $this->flash('Kategori berhasil diperbarui.');
                $this->redirect('kategori');
            } catch (\PDOException $e) {
                $errors[] = $this->pesanDatabase($e);
            }
        }

        $this->dashboard('kategori/edit', [
            'row'    => $input + ['kat_id' => $id],
            'errors' => $errors,
        ]);
    }

    public function hapus()
    {
        $this->wajibPost();

        $id = $this->ambilId($_POST['kat_id'] ?? '');

        try {
            $this->model->hapus($id);
            $this->flash('Kategori berhasil dihapus.');
        } catch (\PDOException $e) {
            // 1451: kategori masih dipakai oleh produk
            if ($this->kodeError($e) !== 1451) {
                throw $e;
            }

            $this->flash('Kategori tidak dapat dihapus karena masih dipakai oleh produk.', 'danger');
        }

        $this->redirect('kategori');
    }

    private function ambilInput(): array
    {
        return [
            'kat_nama'       => $this->post('kat_nama'),
            'kat_keterangan' => $this->post('kat_keterangan'),
        ];
    }

    private function validasi(array $input): array
    {
        $errors = [];

        if ($input['kat_nama'] === '' || mb_strlen($input['kat_nama']) > 50) {
            $errors[] = 'Nama kategori wajib diisi (maksimal 50 karakter).';
        }

        if (mb_strlen($input['kat_keterangan']) > 5000) {
            $errors[] = 'Keterangan maksimal 5000 karakter.';
        }

        return $errors;
    }

    private function pesanDatabase(\PDOException $e): string
    {
        // 1062: nama kategori sudah ada
        return match ($this->kodeError($e)) {
            1062    => 'Nama kategori sudah digunakan.',
            default => throw $e,
        };
    }
}
