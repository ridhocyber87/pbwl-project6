<h2>Detail Penjualan</h2>

<table class="table table-borderless w-auto">
    <tr>
        <th>NO. TRANSAKSI</th>
        <td><?php echo e($data['row']['jual_no']); ?></td>
    </tr>
    <tr>
        <th>TANGGAL</th>
        <td><?php echo e(tanggal($data['row']['jual_tgl'])); ?></td>
    </tr>
    <tr>
        <th>PEMBELI</th>
        <td><?php echo e($data['row']['jual_pembeli']); ?></td>
    </tr>
    <?php if (!empty($data['row']['jual_catatan'])) : ?>
        <tr>
            <th>CATATAN</th>
            <td><?php echo e($data['row']['jual_catatan']); ?></td>
        </tr>
    <?php endif; ?>
</table>

<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>NO</th>
            <th>KODE</th>
            <th>NAMA PRODUK</th>
            <th class="text-end">HARGA</th>
            <th class="text-end">JUMLAH</th>
            <th class="text-end">SUBTOTAL</th>
        </tr>
        <?php foreach ($data['items'] as $no => $item) : ?>
            <tr>
                <td><?php echo $no + 1; ?></td>
                <td><?php echo e($item['detail_kode']); ?></td>
                <td><?php echo e($item['detail_nama']); ?></td>
                <td class="text-end"><?php echo e(rupiah($item['detail_hrg'])); ?></td>
                <td class="text-end"><?php echo (int) $item['detail_qty']; ?></td>
                <td class="text-end"><?php echo e(rupiah($item['detail_subtotal'])); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="5" class="text-end">TOTAL</th>
            <th class="text-end"><?php echo e(rupiah($data['row']['jual_total'])); ?></th>
        </tr>
    </table>
</div>

<div class="no-print">
    <button type="button" class="btn btn-primary" onclick="window.print()">Cetak</button>
    <a href="<?php echo URL; ?>/penjualan" class="btn btn-secondary">Kembali</a>
</div>
