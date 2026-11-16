<?php

// Update November 2024

namespace App\Models;

use App\Core\Model;

class Kategori extends Model
{

      public function tampil()
      {
            $sql = "SELECT * FROM tb_kategori ORDER BY kat_id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $this->selects($stmt);
      }

      public function jumlah(): int
      {
            return (int) $this->db->query("SELECT COUNT(*) FROM tb_kategori")->fetchColumn();
      }

      public function simpan(array $input)
      {
            $sql = "INSERT INTO tb_kategori (kat_nama, kat_keterangan)
                    VALUES (:kat_nama, :kat_keterangan)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                  'kat_nama'       => $input['kat_nama'],
                  'kat_keterangan' => $input['kat_keterangan'],
            ]);
      }

      public function edit(int $id)
      {
            $query = "SELECT * FROM tb_kategori WHERE kat_id=:kat_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['kat_id' => $id]);

            return $this->select($stmt);
      }

      public function update(int $id, array $input)
      {
            $sql = "UPDATE tb_kategori
                    SET kat_nama=:kat_nama, kat_keterangan=:kat_keterangan, updated_at=NOW()
                    WHERE kat_id=:kat_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                  'kat_nama'       => $input['kat_nama'],
                  'kat_keterangan' => $input['kat_keterangan'],
                  'kat_id'         => $id,
            ]);
      }

      public function hapus(int $id)
      {
            $stmt = $this->db->prepare("DELETE FROM tb_kategori WHERE kat_id=:kat_id");
            $stmt->execute(['kat_id' => $id]);
      }
}
