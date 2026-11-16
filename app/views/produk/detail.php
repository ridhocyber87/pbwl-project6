<h2>Detail Produk</h2>

<div class="row">
    <div class="col-md-4 mb-3">
        <?php if (!empty($data['row']['produk_photo'])) : ?>
            <img src="<?php echo URL; ?>/uploads/produk/<?php echo e($data['row']['produk_photo']); ?>"
                alt="<?php echo e($data['row']['produk_nama']); ?>" class="foto-detail img-thumbnail">
        <?php else : ?>
            <p class="text-muted">Belum ada foto.</p>
        <?php endif; ?>
    </div>
    <div class="col-md-8">
        <table class="table table-bordered">
            <tr>
                <th style="width: 30%">KATEGORI</th>
                <td><?php echo e($data['row']['ktgr']); ?></td>
            </tr>
            <tr>
                <th>KODE</th>
                <td><?php echo e($data['row']['produk_kode']); ?></td>
            </tr>
            <tr>
                <th>NAMA</th>
                <td><?php echo e($data['row']['produk_nama']); ?></td>
            </tr>
            <tr>
                <th>HARGA</th>
                <td><?php echo e(rupiah($data['row']['produk_hrg'])); ?></td>
            </tr>
            <tr>
                <th>STOK</th>
                <td><?php echo (int) $data['row']['produk_stock']; ?></td>
            </tr>
            <tr>
                <th>KETERANGAN</th>
                <td><?php echo nl2br(e($data['row']['produk_keterangan'])); ?></td>
            </tr>
        </table>

        <a href="<?php echo URL; ?>/produk/edit/<?php echo (int) $data['row']['produk_id']; ?>" class="btn btn-warning">Edit</a>
        <a href="<?php echo URL; ?>/produk" class="btn btn-secondary">Kembali</a>
    </div>
</div>
