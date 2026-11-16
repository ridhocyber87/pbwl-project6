<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Penjualan as PenjualanModel;

class Penjualan extends Controller
{

    private const MAKS_ITEM = 50;
    private const MAKS_QTY  = 100000;

    private PenjualanModel $model;

    public function __construct()
    {
        $this->model = new PenjualanModel();
    }

    public function index()
    {
        $data['rows'] = $this->model->tampil();
        $this->dashboard('penjualan/index', $data);
    }

    public function input()
    {
        $this->formInput(['jual_tgl' => date('Y-m-d')], []);
    }

    public function simpan()
    {
        $this->wajibPost();

        $input = $this->ambilInput();
        $errors = $this->validasi($input);

        if (!$errors) {
            try {
                $id = $this->model->simpan($input, $this->gabungItem($input['items']));
                $this->flash('Penjualan berhasil disimpan.');
                $this->redirect('penjualan/detail/' . $id);
            } catch (\InvalidArgumentException $e) {
                // Stok tidak mencukupi atau produk tidak ditemukan
                $errors[] = $e->getMessage();
            }
        }

        $this->formInput($input, $errors);
    }

    public function detail($id)
    {
        $id = $this->ambilId($id);
        $row = $this->model->detail($id);

        if ($row === false) {
            $this->flash('Data penjualan tidak ditemukan.', 'danger');
            $this->redirect('penjualan');
        }

        $this->dashboard('penjualan/detail', [
            'row'   => $row,
            'items' => $this->model->item($id),
        ]);
    }

    public function hapus()
    {
        $this->wajibPost();

        $id = $this->ambilId($_POST['jual_id'] ?? '');

        if ($this->model->hapus($id)) {
            $this->flash('Penjualan berhasil dihapus dan stok produk dikembalikan.');
        } else {
            $this->flash('Data penjualan tidak ditemukan.', 'danger');
        }

        $this->redirect('penjualan');
    }

    private function formInput(array $old, array $errors): void
    {
        $this->dashboard('penjualan/input', [
            'listproduk' => $this->model->listProduk(),
            'old'        => $old,
            'errors'     => $errors,
        ]);
    }

    private function ambilInput(): array
    {
        $produk = $_POST['item_produk'] ?? [];
        $jumlah = $_POST['item_qty'] ?? [];
        $items = [];

        if (is_array($produk) && is_array($jumlah)) {
            foreach ($produk as $i => $idProduk) {
                $qty = $jumlah[$i] ?? '';
                $idProduk = is_string($idProduk) ? trim($idProduk) : '';
                $qty = is_string($qty) ? trim($qty) : '';

                // Baris yang produknya belum dipilih diabaikan
                if ($idProduk === '') {
                    continue;
                }

                $items[] = ['produk' => $idProduk, 'qty' => $qty];
            }
        }

        return [
            'jual_tgl'     => $this->post('jual_tgl'),
            'jual_pembeli' => $this->post('jual_pembeli'),
            'jual_catatan' => $this->post('jual_catatan'),
            'items'        => $items,
        ];
    }

    private function validasi(array $input): array
    {
        $errors = [];

        $tgl = \DateTime::createFromFormat('Y-m-d', $input['jual_tgl']);

        if ($tgl === false || $tgl->format('Y-m-d') !== $input['jual_tgl'] || $input['jual_tgl'] < '2000-01-01') {
            $errors[] = 'Tanggal tidak valid.';
        } elseif ($input['jual_tgl'] > date('Y-m-d')) {
            $errors[] = 'Tanggal tidak boleh melebihi hari ini.';
        }

        if ($input['jual_pembeli'] === '' || mb_strlen($input['jual_pembeli']) > 100) {
            $errors[] = 'Nama pembeli wajib diisi (maksimal 100 karakter).';
        }

        if (mb_strlen($input['jual_catatan']) > 255) {
            $errors[] = 'Catatan maksimal 255 karakter.';
        }

        if (!$input['items']) {
            $errors[] = 'Pilih minimal satu produk.';
        } elseif (count($input['items']) > self::MAKS_ITEM) {
            $errors[] = 'Jumlah produk dalam satu penjualan maksimal ' . self::MAKS_ITEM . ' baris.';
        } else {
            foreach ($input['items'] as $no => $item) {
                $baris = $no + 1;

                if (!ctype_digit($item['produk']) || (int) $item['produk'] < 1) {
                    $errors[] = "Produk pada baris {$baris} tidak valid.";
                }

                if (!ctype_digit($item['qty']) || (int) $item['qty'] < 1 || (int) $item['qty'] > self::MAKS_QTY) {
                    $errors[] = "Jumlah pada baris {$baris} harus angka bulat antara 1 dan " . number_format(self::MAKS_QTY, 0, ',', '.') . '.';
                }
            }
        }

        return $errors;
    }

    // Produk yang dipilih lebih dari sekali digabung menjadi satu: [produk_id => jumlah]
    private function gabungItem(array $items): array
    {
        $gabung = [];

        foreach ($items as $item) {
            $id = (int) $item['produk'];
            $gabung[$id] = ($gabung[$id] ?? 0) + (int) $item['qty'];
        }

        return $gabung;
    }
}
