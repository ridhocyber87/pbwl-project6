<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Suendri">
	<title>Project 5 Pemrograman Berbasis Web Lanjutan</title>

	<link href="<?php echo AST; ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo AST; ?>/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
	<link href="<?php echo AST; ?>/css/style.css" rel="stylesheet">
</head>

<body>

	<div class="d-flex" id="wrapper">
		<div class="bg-primary border-right" id="sidebar-wrapper">
			<div class="sidebar-heading text-white">ECOMMERCE</div>

			<div class="list-group list-group-flush">
				<a href="<?php echo URL; ?>/dashboard" class="list-group-item list-group-item-action">Dashboard</a>
				<a href="<?php echo URL; ?>/kategori" class="list-group-item list-group-item-action">Kategori</a>
				<a href="<?php echo URL; ?>/produk" class="list-group-item list-group-item-action">Produk</a>
				<a href="<?php echo URL; ?>/penjualan" class="list-group-item list-group-item-action">Penjualan</a>
			</div>
		</div>
		<div id="page-content-wrapper">
			<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
				<div class="container-fluid">
					<button class="btn btn-primary" id="menu-toggle">
						<i class="bi bi-list"></i>
					</button>
				</div>
			</nav>

			<div class="container-fluid p-4">

				<?php if (!empty($data['flash'])) : ?>
					<div class="alert alert-<?php echo e($data['flash']['tipe']); ?>" role="alert">
						<?php echo e($data['flash']['teks']); ?>
					</div>
				<?php endif; ?>

				<?php if (!empty($data['errors'])) : ?>
					<div class="alert alert-danger" role="alert">
						<ul class="mb-0">
							<?php foreach ($data['errors'] as $pesan) : ?>
								<li><?php echo e($pesan); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php require ROOT . "app/views/" . $view . ".php"; ?>

			</div>
		</div>
	</div>

	<script src="<?php echo AST; ?>/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php echo AST; ?>/js/app.js"></script>

</body>

</html>