<?php

namespace App\Models;

use App\Core\Model;

class Penjualan extends Model
{

      public function tampil()
      {
            $sql = "SELECT j.jual_id, j.jual_no, j.jual_tgl, j.jual_pembeli, j.jual_total,
                           (SELECT COUNT(*) FROM tb_penjualan_detail d WHERE d.detail_id_jual = j.jual_id) AS jumlah_item
                    FROM tb_penjualan j
                    ORDER BY j.jual_tgl DESC, j.jual_id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $this->selects($stmt);
      }

      // Produk yang bisa dipilih pada form penjualan
      public function listProduk()
      {
            $sql = "SELECT produk_id, produk_kode, produk_nama, produk_hrg, produk_stock
                    FROM tb_produk
                    ORDER BY produk_nama ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $this->selects($stmt);
      }

      // Jumlah transaksi dan total penjualan untuk dashboard
      public function ringkasan()
      {
            $sql = "SELECT COUNT(*) AS jumlah, COALESCE(SUM(jual_total), 0) AS omzet FROM tb_penjualan";

            return $this->db->query($sql)->fetch();
      }

      public function detail(int $id)
      {
            $stmt = $this->db->prepare("SELECT * FROM tb_penjualan WHERE jual_id=:jual_id");
            $stmt->execute(['jual_id' => $id]);

            return $this->select($stmt);
      }

      public function item(int $id)
      {
            $sql = "SELECT * FROM tb_penjualan_detail WHERE detail_id_jual=:jual_id ORDER BY detail_id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['jual_id' => $id]);

            return $this->selects($stmt);
      }

      /**
       * Menyimpan penjualan beserta itemnya dan mengurangi stok.
       * $items berbentuk [produk_id => jumlah]. Semua proses berada dalam satu
       * transaksi database, jika ada yang gagal semuanya dibatalkan.
       */
      public function simpan(array $header, array $items): int
      {
            // Urutan tetap supaya dua transaksi bersamaan tidak saling mengunci
            ksort($items);

            $this->db->beginTransaction();

            try {
                  $cari = $this->db->prepare(
                        "SELECT produk_id, produk_kode, produk_nama, produk_hrg, produk_stock
                         FROM tb_produk
                         WHERE produk_id=:produk_id
                         FOR UPDATE"
                  );

                  $baris = [];
                  $total = 0;

                  foreach ($items as $idProduk => $qty) {
                        // FOR UPDATE mengunci baris produk sampai transaksi selesai
                        $cari->execute(['produk_id' => $idProduk]);
                        $produk = $cari->fetch();
                        $cari->closeCursor();

                        if ($produk === false) {
                              throw new \InvalidArgumentException('Produk yang dipilih tidak ditemukan.');
                        }

                        if ((int) $produk['produk_stock'] < $qty) {
                              throw new \InvalidArgumentException(
                                    "Stok {$produk['produk_nama']} tidak mencukupi (tersisa {$produk['produk_stock']})."
                              );
                        }

                        // Harga dan nama dicatat saat transaksi, agar nota tidak berubah bila produk diedit
                        $subtotal = (int) $produk['produk_hrg'] * $qty;
                        $total += $subtotal;

                        $baris[] = [
                              'detail_id_produk' => (int) $produk['produk_id'],
                              'detail_kode'      => $produk['produk_kode'],
                              'detail_nama'      => $produk['produk_nama'],
                              'detail_hrg'       => (int) $produk['produk_hrg'],
                              'detail_qty'       => $qty,
                              'detail_subtotal'  => $subtotal,
                        ];
                  }

                  // Nomor sementara, nomor akhir dibuat dari id supaya pasti unik
                  $sql = "INSERT INTO tb_penjualan (jual_no, jual_tgl, jual_pembeli, jual_catatan, jual_total)
                          VALUES (:jual_no, :jual_tgl, :jual_pembeli, :jual_catatan, :jual_total)";
                  $stmt = $this->db->prepare($sql);
                  $stmt->execute([
                        'jual_no'      => 'TMP-' . bin2hex(random_bytes(8)),
                        'jual_tgl'     => $header['jual_tgl'],
                        'jual_pembeli' => $header['jual_pembeli'],
                        'jual_catatan' => $header['jual_catatan'] !== '' ? $header['jual_catatan'] : null,
                        'jual_total'   => $total,
                  ]);

                  $id = (int) $this->db->lastInsertId();
                  $nomor = sprintf('INV-%s-%05d', str_replace('-', '', $header['jual_tgl']), $id);

                  $stmt = $this->db->prepare("UPDATE tb_penjualan SET jual_no=:jual_no WHERE jual_id=:jual_id");
                  $stmt->execute(['jual_no' => $nomor, 'jual_id' => $id]);

                  $sqlDetail = "INSERT INTO tb_penjualan_detail
                                    (detail_id_jual, detail_id_produk, detail_kode, detail_nama, detail_hrg, detail_qty, detail_subtotal)
                                VALUES
                                    (:detail_id_jual, :detail_id_produk, :detail_kode, :detail_nama, :detail_hrg, :detail_qty, :detail_subtotal)";
                  $stmtDetail = $this->db->prepare($sqlDetail);

                  $stmtStok = $this->db->prepare(
                        "UPDATE tb_produk SET produk_stock = produk_stock - :qty WHERE produk_id=:produk_id"
                  );

                  foreach ($baris as $b) {
                        $stmtDetail->execute($b + ['detail_id_jual' => $id]);
                        $stmtStok->execute(['qty' => $b['detail_qty'], 'produk_id' => $b['detail_id_produk']]);
                  }

                  $this->db->commit();

                  return $id;
            } catch (\Throwable $e) {
                  if ($this->db->inTransaction()) {
                        $this->db->rollBack();
                  }

                  throw $e;
            }
      }

      /**
       * Menghapus penjualan dan mengembalikan stok produk.
       * Mengembalikan false jika penjualan tidak ditemukan.
       */
      public function hapus(int $id): bool
      {
            $this->db->beginTransaction();

            try {
                  $stmt = $this->db->prepare("SELECT jual_id FROM tb_penjualan WHERE jual_id=:jual_id FOR UPDATE");
                  $stmt->execute(['jual_id' => $id]);

                  if ($stmt->fetch() === false) {
                        $this->db->rollBack();

                        return false;
                  }

                  $stmt->closeCursor();

                  $stmtStok = $this->db->prepare(
                        "UPDATE tb_produk SET produk_stock = produk_stock + :qty WHERE produk_id=:produk_id"
                  );

                  foreach ($this->item($id) as $item) {
                        $stmtStok->execute([
                              'qty'       => $item['detail_qty'],
                              'produk_id' => $item['detail_id_produk'],
                        ]);
                  }

                  // Baris detail ikut terhapus (ON DELETE CASCADE)
                  $stmt = $this->db->prepare("DELETE FROM tb_penjualan WHERE jual_id=:jual_id");
                  $stmt->execute(['jual_id' => $id]);

                  $this->db->commit();

                  return true;
            } catch (\Throwable $e) {
                  if ($this->db->inTransaction()) {
                        $this->db->rollBack();
                  }

                  throw $e;
            }
      }
}
