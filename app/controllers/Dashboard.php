<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Produk;

class Dashboard extends Controller
{

	public function index()
	{
		$penjualan = (new Penjualan())->ringkasan();

		$data['jumlah_kategori'] = (new Kategori())->jumlah();
		$data['jumlah_produk'] = (new Produk())->jumlah();
		$data['jumlah_penjualan'] = (int) $penjualan['jumlah'];
		$data['total_penjualan'] = (int) $penjualan['omzet'];

		$this->dashboard('dashboard/index', $data);
	}
}
