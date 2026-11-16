<?php

namespace App\Models;

use App\Core\Model;

class Produk extends Model
{

      public function tampil()
      {
            $sql = "SELECT p.*, k.kat_nama AS ktgr
                    FROM tb_produk p
                    LEFT JOIN tb_kategori k ON p.produk_id_kat = k.kat_id
                    ORDER BY p.produk_id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $this->selects($stmt);
      }

      public function listKategori()
      {
            $stmt = $this->db->prepare("SELECT kat_id, kat_nama FROM tb_kategori ORDER BY kat_nama ASC");
            $stmt->execute();

            return $this->selects($stmt);
      }

      public function jumlah(): int
      {
            return (int) $this->db->query("SELECT COUNT(*) FROM tb_produk")->fetchColumn();
      }

      // Satu produk beserta nama kategorinya (dipakai oleh edit dan detail)
      public function edit(int $id)
      {
            $sql = "SELECT p.*, k.kat_nama AS ktgr
                    FROM tb_produk p
                    LEFT JOIN tb_kategori k ON p.produk_id_kat = k.kat_id
                    WHERE p.produk_id=:produk_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['produk_id' => $id]);

            return $this->select($stmt);
      }

      public function simpan(array $input)
      {
            $sql = "INSERT INTO tb_produk
                        (produk_id_kat, produk_kode, produk_nama, produk_hrg, produk_keterangan, produk_stock, produk_photo)
                    VALUES
                        (:produk_id_kat, :produk_kode, :produk_nama, :produk_hrg, :produk_keterangan, :produk_stock, :produk_photo)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($this->kolom($input));
      }

      public function update(int $id, array $input)
      {
            $sql = "UPDATE tb_produk
                    SET produk_id_kat=:produk_id_kat, produk_kode=:produk_kode, produk_nama=:produk_nama,
                        produk_hrg=:produk_hrg, produk_keterangan=:produk_keterangan,
                        produk_stock=:produk_stock, produk_photo=:produk_photo, updated_at=NOW()
                    WHERE produk_id=:produk_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($this->kolom($input) + ['produk_id' => $id]);
      }

      public function hapus(int $id)
      {
            $stmt = $this->db->prepare("DELETE FROM tb_produk WHERE produk_id=:produk_id");
            $stmt->execute(['produk_id' => $id]);
      }

      // Menyusun nilai yang akan disimpan, keterangan dan foto boleh kosong (NULL)
      private function kolom(array $input): array
      {
            return [
                  'produk_id_kat'     => (int) $input['produk_id_kat'],
                  'produk_kode'       => $input['produk_kode'],
                  'produk_nama'       => $input['produk_nama'],
                  'produk_hrg'        => (int) $input['produk_hrg'],
                  'produk_keterangan' => $input['produk_keterangan'] !== '' ? $input['produk_keterangan'] : null,
                  'produk_stock'      => (int) $input['produk_stock'],
                  'produk_photo'      => $input['produk_photo'] ?? null,
            ];
      }
}
