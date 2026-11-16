<?php
// Isi formulir produk, dipakai oleh input.php dan edit.php
// Variabel yang dibutuhkan: $row (nilai isian), $aksi, $tombol
?>
<form action="<?php echo URL; ?>/produk/<?php echo $aksi; ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if (isset($row['produk_id'])) : ?>
        <input type="hidden" name="produk_id" value="<?php echo (int) $row['produk_id']; ?>">
    <?php endif; ?>

    <table class="table table-hover">
        <tr>
            <td>KATEGORI</td>
            <td>
                <select name="produk_id_kat" class="form-select" required>
                    <option value="">-- Pilih kategori --</option>
                    <?php foreach ($data['listkategori'] as $kat) : ?>
                        <option value="<?php echo (int) $kat['kat_id']; ?>"
                            <?php echo ((string) ($row['produk_id_kat'] ?? '') === (string) $kat['kat_id']) ? 'selected' : ''; ?>>
                            <?php echo e($kat['kat_nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($data['listkategori'])) : ?>
                    <div class="form-text">Belum ada kategori. <a href="<?php echo URL; ?>/kategori/input">Input kategori</a> terlebih dahulu.</div>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>KODE</td>
            <td><input type="text" name="produk_kode" class="form-control" maxlength="50" required
                    value="<?php echo e($row['produk_kode'] ?? ''); ?>"></td>
        </tr>
        <tr>
            <td>NAMA</td>
            <td><input type="text" name="produk_nama" class="form-control" maxlength="256" required
                    value="<?php echo e($row['produk_nama'] ?? ''); ?>"></td>
        </tr>
        <tr>
            <td>HARGA</td>
            <td><input type="number" name="produk_hrg" class="form-control" min="0" max="999999999" step="1" required
                    value="<?php echo e($row['produk_hrg'] ?? ''); ?>"></td>
        </tr>
        <tr>
            <td>STOK</td>
            <td><input type="number" name="produk_stock" class="form-control" min="0" max="1000000" step="1" required
                    value="<?php echo e($row['produk_stock'] ?? '0'); ?>"></td>
        </tr>
        <tr>
            <td>KETERANGAN</td>
            <td><textarea name="produk_keterangan" class="form-control" cols="30" rows="6"><?php echo e($row['produk_keterangan'] ?? ''); ?></textarea></td>
        </tr>
        <tr>
            <td>FOTO</td>
            <td>
                <?php if (!empty($row['produk_photo'])) : ?>
                    <p>
                        <img src="<?php echo URL; ?>/uploads/produk/<?php echo e($row['produk_photo']); ?>"
                            alt="Foto produk" class="foto-produk">
                    </p>
                <?php endif; ?>
                <input type="file" name="produk_photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                <div class="form-text">
                    Format JPG, PNG, atau WEBP, maksimal 2 MB.
                    <?php echo !empty($row['produk_photo']) ? 'Kosongkan jika tidak ingin mengganti foto.' : ''; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" value="<?php echo $tombol; ?>" class="btn btn-primary">
                <a href="<?php echo URL; ?>/produk" class="btn btn-secondary">KEMBALI</a>
            </td>
        </tr>
    </table>
</form>
