<h2>Produk</h2>

<a href="<?php echo URL; ?>/produk/input" class="btn btn-primary mb-3">Input Produk</a>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tr>
            <th>NO</th>
            <th>FOTO</th>
            <th>KATEGORI</th>
            <th>KODE</th>
            <th>NAMA</th>
            <th>HARGA</th>
            <th>STOK</th>
            <th>AKSI</th>
        </tr>
        <?php foreach ($data['rows'] as $no => $row) : ?>
            <tr>
                <td><?php echo $no + 1; ?></td>
                <td>
                    <?php if (!empty($row['produk_photo'])) : ?>
                        <img src="<?php echo URL; ?>/uploads/produk/<?php echo e($row['produk_photo']); ?>"
                            alt="<?php echo e($row['produk_nama']); ?>" class="foto-produk">
                    <?php else : ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?php echo e($row['ktgr']); ?></td>
                <td><?php echo e($row['produk_kode']); ?></td>
                <td><?php echo e($row['produk_nama']); ?></td>
                <td><?php echo e(rupiah($row['produk_hrg'])); ?></td>
                <td><?php echo (int) $row['produk_stock']; ?></td>
                <td class="text-nowrap">
                    <a href="<?php echo URL; ?>/produk/detail/<?php echo (int) $row['produk_id']; ?>" class="btn btn-info btn-sm">Detail</a>
                    <a href="<?php echo URL; ?>/produk/edit/<?php echo (int) $row['produk_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <form action="<?php echo URL; ?>/produk/hapus" method="post" class="d-inline"
                        onsubmit="return confirm('Yakin hapus produk ini?')">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="produk_id" value="<?php echo (int) $row['produk_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($data['rows'])) : ?>
            <tr>
                <td colspan="8">Belum ada data produk</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
