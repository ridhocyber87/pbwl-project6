<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Upload;
use App\Models\Produk as ProdukModel;

class Produk extends Controller
{

    private ProdukModel $model;

    public function __construct()
    {
        $this->model = new ProdukModel();
    }

    public function index()
    {
        $data['rows'] = $this->model->tampil();
        $this->dashboard('produk/index', $data);
    }

    public function input()
    {
        $this->formInput([], []);
    }

    public function simpan()
    {
        $this->wajibPost();

        $input = $this->ambilInput();
        $errors = $this->validasi($input);
        $foto = null;

        if (!$errors) {
            try {
                $foto = Upload::gambar($_FILES['produk_photo'] ?? null);
            } catch (\InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!$errors) {
            try {
                $this->model->simpan($input + ['produk_photo' => $foto]);
                $this->flash('Produk berhasil disimpan.');
                $this->redirect('produk');
            } catch (\PDOException $e) {
                // Data gagal disimpan, foto yang terlanjur diunggah ikut dibuang
                Upload::hapus($foto);
                $errors[] = $this->pesanDatabase($e);
            }
        }

        $this->formInput($input, $errors);
    }

    public function detail($id)
    {
        $row = $this->model->edit($this->ambilId($id));

        if ($row === false) {
            $this->flash('Data produk tidak ditemukan.', 'danger');
            $this->redirect('produk');
        }

        $this->dashboard('produk/detail', ['row' => $row]);
    }

    public function edit($id)
    {
        $row = $this->model->edit($this->ambilId($id));

        if ($row === false) {
            $this->flash('Data produk tidak ditemukan.', 'danger');
            $this->redirect('produk');
        }

        $this->formEdit($row, []);
    }

    public function update()
    {
        $this->wajibPost();

        $id = $this->ambilId($_POST['produk_id'] ?? '');
        $lama = $this->model->edit($id);

        if ($lama === false) {
            $this->flash('Data produk tidak ditemukan.', 'danger');
            $this->redirect('produk');
        }

        $input = $this->ambilInput();
        $errors = $this->validasi($input);
        $fotoBaru = null;

        if (!$errors) {
            try {
                $fotoBaru = Upload::gambar($_FILES['produk_photo'] ?? null);
            } catch (\InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!$errors) {
            try {
                // Tanpa foto baru, foto lama tetap dipakai
                $this->model->update($id, $input + ['produk_photo' => $fotoBaru ?? $lama['produk_photo']]);

                if ($fotoBaru !== null) {
                    Upload::hapus($lama['produk_photo']);
                }

                $this->flash('Produk berhasil diperbarui.');
                $this->redirect('produk');
            } catch (\PDOException $e) {
                Upload::hapus($fotoBaru);
                $errors[] = $this->pesanDatabase($e);
            }
        }

        $this->formEdit($input + $lama, $errors);
    }

    public function hapus()
    {
        $this->wajibPost();

        $id = $this->ambilId($_POST['produk_id'] ?? '');
        $row = $this->model->edit($id);

        if ($row === false) {
            $this->flash('Data produk tidak ditemukan.', 'danger');
        } else {
            try {
                $this->model->hapus($id);
                Upload::hapus($row['produk_photo']);
                $this->flash('Produk berhasil dihapus.');
            } catch (\PDOException $e) {
                // 1451: produk sudah tercatat dalam penjualan
                if ($this->kodeError($e) !== 1451) {
                    throw $e;
                }

                $this->flash('Produk tidak dapat dihapus karena sudah tercatat dalam penjualan.', 'danger');
            }
        }

        $this->redirect('produk');
    }

    private function formInput(array $old, array $errors): void
    {
        $this->dashboard('produk/input', [
            'listkategori' => $this->model->listKategori(),
            'old'          => $old,
            'errors'       => $errors,
        ]);
    }

    private function formEdit(array $row, array $errors): void
    {
        $this->dashboard('produk/edit', [
            'listkategori' => $this->model->listKategori(),
            'row'          => $row,
            'errors'       => $errors,
        ]);
    }

    private function ambilInput(): array
    {
        return [
            'produk_id_kat'     => $this->post('produk_id_kat'),
            'produk_kode'       => $this->post('produk_kode'),
            'produk_nama'       => $this->post('produk_nama'),
            'produk_hrg'        => $this->post('produk_hrg'),
            'produk_keterangan' => $this->post('produk_keterangan'),
            'produk_stock'      => $this->post('produk_stock'),
        ];
    }

    private function validasi(array $input): array
    {
        $errors = [];

        if (!ctype_digit($input['produk_id_kat']) || (int) $input['produk_id_kat'] < 1) {
            $errors[] = 'Kategori wajib dipilih.';
        }

        if (
            $input['produk_kode'] === ''
            || mb_strlen($input['produk_kode']) > 50
            || !preg_match('/^[A-Za-z0-9._-]+$/', $input['produk_kode'])
        ) {
            $errors[] = 'Kode produk wajib diisi (maksimal 50 karakter, hanya huruf, angka, titik, garis bawah, dan tanda hubung).';
        }

        if ($input['produk_nama'] === '' || mb_strlen($input['produk_nama']) > 256) {
            $errors[] = 'Nama produk wajib diisi (maksimal 256 karakter).';
        }

        if (!ctype_digit($input['produk_hrg']) || (int) $input['produk_hrg'] > 999999999) {
            $errors[] = 'Harga harus berupa angka bulat antara 0 dan 999.999.999.';
        }

        if (!ctype_digit($input['produk_stock']) || (int) $input['produk_stock'] > 1000000) {
            $errors[] = 'Stok harus berupa angka bulat antara 0 dan 1.000.000.';
        }

        if (mb_strlen($input['produk_keterangan']) > 5000) {
            $errors[] = 'Keterangan maksimal 5000 karakter.';
        }

        return $errors;
    }

    private function pesanDatabase(\PDOException $e): string
    {
        // 1062: kode produk ganda, 1452: kategori tidak ada
        return match ($this->kodeError($e)) {
            1062    => 'Kode produk sudah digunakan.',
            1452    => 'Kategori yang dipilih tidak valid.',
            default => throw $e,
        };
    }
}
