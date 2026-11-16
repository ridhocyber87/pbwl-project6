// Form penjualan: tambah atau hapus baris produk dan hitung subtotal serta total
(function () {
    var MAKS_BARIS = 50;

    var body = document.getElementById('baris-item');
    var templat = document.getElementById('template-baris');
    var total = document.getElementById('total-penjualan');
    var tambah = document.getElementById('tambah-baris');

    if (!body || !templat || !total || !tambah) {
        return;
    }

    function rupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    function hitung() {
        var jumlah = 0;

        body.querySelectorAll('.baris').forEach(function (baris) {
            var pilihan = baris.querySelector('.item-produk').selectedOptions[0];
            var inputQty = baris.querySelector('.item-qty');
            var harga = pilihan && pilihan.dataset.harga ? parseInt(pilihan.dataset.harga, 10) : 0;
            var stok = pilihan && pilihan.dataset.stok ? parseInt(pilihan.dataset.stok, 10) : 0;
            var qty = parseInt(inputQty.value, 10) || 0;

            // Batas jumlah mengikuti stok (pemeriksaan sebenarnya tetap di server)
            inputQty.max = stok > 0 ? stok : 100000;

            baris.querySelector('.harga').textContent = harga ? rupiah(harga) : '-';
            baris.querySelector('.subtotal').textContent = harga ? rupiah(harga * qty) : '-';

            jumlah += harga * qty;
        });

        total.textContent = rupiah(jumlah);
    }

    tambah.addEventListener('click', function () {
        if (body.querySelectorAll('.baris').length >= MAKS_BARIS) {
            return;
        }

        body.appendChild(templat.content.cloneNode(true));
        hitung();
    });

    body.addEventListener('click', function (e) {
        var tombol = e.target.closest('.hapus-baris');

        // Baris terakhir tidak dihapus supaya form selalu punya satu baris
        if (tombol && body.querySelectorAll('.baris').length > 1) {
            tombol.closest('.baris').remove();
            hitung();
        }
    });

    body.addEventListener('input', hitung);
    body.addEventListener('change', hitung);

    hitung();
})();
