<h2>Dashboard</h2>

<div class="alert alert-info">Selamat datang, <strong>Suendri</strong></div>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted">Jumlah Kategori</h6>
                <p class="display-6 mb-0"><?php echo (int) $data['jumlah_kategori']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted">Jumlah Produk</h6>
                <p class="display-6 mb-0"><?php echo (int) $data['jumlah_produk']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted">Jumlah Transaksi</h6>
                <p class="display-6 mb-0"><?php echo (int) $data['jumlah_penjualan']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted">Total Penjualan</h6>
                <p class="h3 mb-0"><?php echo e(rupiah($data['total_penjualan'])); ?></p>
            </div>
        </div>
    </div>
</div>
