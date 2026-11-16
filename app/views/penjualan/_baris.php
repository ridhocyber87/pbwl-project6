<?php
// Satu baris produk pada form penjualan, dipakai untuk baris awal dan template JavaScript
// Variabel yang dibutuhkan: $item (produk dan qty)
?>
<tr class="baris">
    <td>
        <select name="item_produk[]" class="form-select item-produk" required>
            <option value="">-- Pilih produk --</option>
            <?php foreach ($data['listproduk'] as $p) : ?>
                <?php $dipilih = (string) $item['produk'] === (string) $p['produk_id']; ?>
                <option value="<?php echo (int) $p['produk_id']; ?>"
                    data-harga="<?php echo (int) $p['produk_hrg']; ?>"
                    data-stok="<?php echo (int) $p['produk_stock']; ?>"
                    <?php echo $dipilih ? 'selected' : ''; ?>
                    <?php echo ((int) $p['produk_stock'] < 1 && !$dipilih) ? 'disabled' : ''; ?>>
                    <?php echo e($p['produk_kode'] . ' - ' . $p['produk_nama'] . ' (stok ' . (int) $p['produk_stock'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td class="harga text-end">-</td>
    <td style="width: 140px">
        <input type="number" name="item_qty[]" class="form-control item-qty" min="1" max="100000" step="1" required
            value="<?php echo e($item['qty']); ?>">
    </td>
    <td class="subtotal text-end">-</td>
    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm hapus-baris" title="Hapus baris">&times;</button>
    </td>
</tr>
