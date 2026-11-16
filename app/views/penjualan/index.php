<h2>Penjualan</h2>

<a href="<?php echo URL; ?>/penjualan/input" class="btn btn-primary mb-3">Input Penjualan</a>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tr>
            <th>NO</th>
            <th>NO. TRANSAKSI</th>
            <th>TANGGAL</th>
            <th>PEMBELI</th>
            <th>ITEM</th>
            <th>TOTAL</th>
            <th>AKSI</th>
        </tr>
        <?php foreach ($data['rows'] as $no => $row) : ?>
            <tr>
                <td><?php echo $no + 1; ?></td>
                <td><?php echo e($row['jual_no']); ?></td>
                <td><?php echo e(tanggal($row['jual_tgl'])); ?></td>
                <td><?php echo e($row['jual_pembeli']); ?></td>
                <td><?php echo (int) $row['jumlah_item']; ?></td>
                <td><?php echo e(rupiah($row['jual_total'])); ?></td>
                <td class="text-nowrap">
                    <a href="<?php echo URL; ?>/penjualan/detail/<?php echo (int) $row['jual_id']; ?>" class="btn btn-info btn-sm">Detail</a>
                    <form action="<?php echo URL; ?>/penjualan/hapus" method="post" class="d-inline"
                        onsubmit="return confirm('Yakin hapus penjualan ini? Stok produk akan dikembalikan.')">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="jual_id" value="<?php echo (int) $row['jual_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($data['rows'])) : ?>
            <tr>
                <td colspan="7">Belum ada data penjualan</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
